<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FinanceController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewFinances', Booking::class);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'status' => ['nullable', Rule::in(Booking::STATUSES)],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $bookings = Booking::query()
            ->with([
                'user:id,name,email',
                'room:id,room_number,room_type',
                'payments' => fn ($query) => $query->with('recordedBy:id,name')->latest('paid_at'),
            ])
            ->withSum('receivedPayments', 'amount')
            ->withSum('refunds', 'amount')
            ->when($filters['search'] ?? null, function ($query, string $search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested
                        ->whereHas('user', fn ($user) => $user
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%"))
                        ->orWhereHas('room', fn ($room) => $room->where('room_number', 'like', "%{$search}%"));
                });
            })
            ->when($filters['status'] ?? null, fn ($query, string $status) => $query->where('status', $status))
            ->when($filters['date_from'] ?? null, fn ($query, string $date) => $query->whereDate('check_out_date', '>=', $date))
            ->when($filters['date_to'] ?? null, fn ($query, string $date) => $query->whereDate('check_in_date', '<=', $date))
            ->orderByDesc('check_in_date')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $confirmedBookings = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->withSum('receivedPayments', 'amount')
            ->withSum('refunds', 'amount')
            ->get();

        $received = (float) Payment::where('kind', Payment::KIND_PAYMENT)->sum('amount');
        $refunded = (float) Payment::where('kind', Payment::KIND_REFUND)->sum('amount');
        $netCollected = max(0, $received - $refunded);

        $summary = [
            'confirmed_revenue' => $confirmedBookings->sum(fn (Booking $booking): float => $booking->totalAmount()),
            'projected_revenue' => (float) Booking::whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])->sum('total_amount'),
            'net_collected' => $netCollected,
            'pending_balance' => $confirmedBookings->sum(fn (Booking $booking): float => $booking->balanceDue()),
            'refunds' => $refunded,
            'occupancy' => $this->currentMonthOccupancy(),
        ];

        $monthlyPerformance = $this->monthlyPerformance();
        $chartMaximum = max(1, $monthlyPerformance->max(fn (array $month): float => max($month['sales'], $month['collected'])));

        return view('finances.index', compact(
            'bookings',
            'chartMaximum',
            'filters',
            'monthlyPerformance',
            'summary',
        ));
    }

    private function currentMonthOccupancy(): float
    {
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth()->addDay();
        $roomNights = max(1, Room::count() * $monthStart->daysInMonth);

        $occupiedNights = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->where('check_in_date', '<', $monthEnd->toDateString())
            ->where('check_out_date', '>', $monthStart->toDateString())
            ->get(['check_in_date', 'check_out_date'])
            ->sum(function (Booking $booking) use ($monthStart, $monthEnd): int {
                $start = $booking->check_in_date->greaterThan($monthStart) ? $booking->check_in_date : $monthStart;
                $end = $booking->check_out_date->lessThan($monthEnd) ? $booking->check_out_date : $monthEnd;

                return max(0, $start->diffInDays($end));
            });

        return min(100, round(($occupiedNights / $roomNights) * 100, 1));
    }

    /** @return Collection<int, array{label: string, sales: float, collected: float}> */
    private function monthlyPerformance(): Collection
    {
        $periodStart = now()->subMonths(5)->startOfMonth();
        $periodEnd = now()->endOfMonth();

        $sales = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereBetween('check_in_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
            ->get(['check_in_date', 'total_amount'])
            ->groupBy(fn (Booking $booking): string => $booking->check_in_date->format('Y-m'));

        $payments = Payment::query()
            ->whereBetween('paid_at', [$periodStart, $periodEnd])
            ->get(['paid_at', 'kind', 'amount'])
            ->groupBy(fn (Payment $payment): string => $payment->paid_at->format('Y-m'));

        return collect(range(0, 5))->map(function (int $monthsAgo) use ($sales, $payments): array {
            $month = now()->subMonths(5 - $monthsAgo)->startOfMonth();
            $key = $month->format('Y-m');
            $monthPayments = $payments->get($key, collect());
            $collected = (float) $monthPayments->where('kind', Payment::KIND_PAYMENT)->sum('amount')
                - (float) $monthPayments->where('kind', Payment::KIND_REFUND)->sum('amount');

            return [
                'label' => Carbon::parse($month)->locale('es')->translatedFormat('M Y'),
                'sales' => (float) $sales->get($key, collect())->sum('total_amount'),
                'collected' => max(0, $collected),
            ];
        });
    }
}
