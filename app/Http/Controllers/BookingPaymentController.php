<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBookingPaymentRequest;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class BookingPaymentController extends Controller
{
    public function store(StoreBookingPaymentRequest $request, Booking $booking): RedirectResponse
    {
        Gate::authorize('recordPayment', $booking);

        DB::transaction(function () use ($request, $booking): void {
            $lockedBooking = Booking::query()
                ->withSum('receivedPayments', 'amount')
                ->withSum('refunds', 'amount')
                ->lockForUpdate()
                ->findOrFail($booking->id);

            $data = $request->safe()->only([
                'kind',
                'amount',
                'method',
                'reference',
                'notes',
                'paid_at',
            ]);
            $amount = (float) $data['amount'];

            if ($data['kind'] === Payment::KIND_PAYMENT) {
                if ($lockedBooking->status === Booking::STATUS_CANCELLED) {
                    throw ValidationException::withMessages([
                        'amount' => 'No se pueden registrar cobros en una reservación cancelada.',
                    ]);
                }

                if ($amount > $lockedBooking->balanceDue() + 0.001) {
                    throw ValidationException::withMessages([
                        'amount' => 'El cobro supera el saldo pendiente de la reservación.',
                    ]);
                }
            }

            if ($data['kind'] === Payment::KIND_REFUND && $amount > $lockedBooking->netPaid() + 0.001) {
                throw ValidationException::withMessages([
                    'amount' => 'El reembolso supera el valor pagado disponible.',
                ]);
            }

            $lockedBooking->payments()->create([
                ...$data,
                'recorded_by' => $request->user()->id,
            ]);
        });

        return redirect()
            ->route('finances.index')
            ->with('status', 'Movimiento financiero registrado correctamente.');
    }
}
