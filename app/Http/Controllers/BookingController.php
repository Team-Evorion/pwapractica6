<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\UpdateBookingStatusRequest;
use App\Models\Booking;
use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Booking::class);

        $filters = $request->validate([
            'status' => ['nullable', 'string', Rule::in(Booking::STATUSES)],
            'customer_id' => ['nullable', 'integer', 'exists:users,id'],
            'check_in_date' => ['nullable', 'date'],
            'check_out_date' => ['nullable', 'date', 'after:check_in_date'],
        ]);

        $user = $request->user();
        $isCustomer = $user->hasRole(Role::CUSTOMER);
        $canManage = $user->hasRole(Role::ADMINISTRATOR, Role::RECEPTIONIST);

        $bookings = Booking::query()
            ->with(['room', 'user.role'])
            ->when($isCustomer, fn ($query) => $query->where('user_id', $user->id))
            ->when(! $isCustomer && ! empty($filters['customer_id']), fn ($query) => $query->where('user_id', $filters['customer_id']))
            ->when(! empty($filters['status']), fn ($query) => $query->where('status', $filters['status']))
            ->when(! empty($filters['check_in_date']), fn ($query) => $query->where('check_out_date', '>', $filters['check_in_date']))
            ->when(! empty($filters['check_out_date']), fn ($query) => $query->where('check_in_date', '<', $filters['check_out_date']))
            ->orderByDesc('booking_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $customers = ! $isCustomer
            ? User::whereHas('role', fn ($query) => $query->where('name', Role::CUSTOMER))
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
            : collect();

        $availableRooms = collect();
        if (Gate::allows('create', Booking::class)) {
            $roomQuery = Room::query()->where('is_available', true)->orderBy('room_number');
            if (! empty($filters['check_in_date']) && ! empty($filters['check_out_date'])) {
                $roomQuery->availableBetween($filters['check_in_date'], $filters['check_out_date']);
            }
            $availableRooms = $roomQuery->get();
        }

        return view('bookings.index', compact(
            'availableRooms',
            'bookings',
            'canManage',
            'customers',
            'filters',
            'isCustomer',
        ));
    }

    public function store(StoreBookingRequest $request): RedirectResponse
    {
        Gate::authorize('create', Booking::class);

        $validated = $request->validated();
        $actor = $request->user();
        $customer = $actor->hasRole(Role::CUSTOMER)
            ? $actor
            : User::findOrFail($validated['user_id']);

        DB::transaction(function () use ($validated, $customer): void {
            $room = Room::whereKey($validated['room_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (! $room->is_available) {
                throw ValidationException::withMessages([
                    'room_id' => 'La habitación no está disponible operativamente.',
                ]);
            }

            $overlap = Booking::where('room_id', $room->id)
                ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
                ->where('check_in_date', '<', $validated['check_out_date'])
                ->where('check_out_date', '>', $validated['check_in_date'])
                ->exists();

            if ($overlap) {
                throw ValidationException::withMessages([
                    'check_in_date' => 'La habitación ya está reservada para esas fechas.',
                ]);
            }

            $customer->bookings()->create([
                'room_id' => $room->id,
                'booking_date' => now()->toDateString(),
                'check_in_date' => $validated['check_in_date'],
                'check_out_date' => $validated['check_out_date'],
                'status' => Booking::STATUS_PENDING,
            ]);
        });

        return redirect()
            ->route('bookings.index')
            ->with('status', 'Reserva registrada correctamente y pendiente de confirmación.');
    }

    public function updateStatus(UpdateBookingStatusRequest $request, Booking $booking): RedirectResponse
    {
        Gate::authorize('update', $booking);

        if ($booking->status === Booking::STATUS_CANCELLED) {
            return back()->withErrors([
                'status' => 'Una reserva cancelada no puede volver a activarse.',
            ]);
        }

        $booking->update(['status' => $request->validated('status')]);

        return redirect()
            ->route('bookings.index')
            ->with('status', 'Estado de la reservación actualizado correctamente.');
    }
}
