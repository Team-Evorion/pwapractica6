<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $bookings = $request->user()
            ->bookings()
            ->with('room')
            ->latest()
            ->get();

        return view('bookings.index', compact('bookings'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'check_in_date' => ['required', 'date', 'after_or_equal:today'],
            'check_out_date' => ['required', 'date', 'after:check_in_date'],
        ]);

        $user = $request->user();

        DB::transaction(function () use ($validated, $user): void {
            $room = Room::whereKey($validated['room_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (! $room->is_available) {
                throw ValidationException::withMessages([
                    'room_id' => 'La habitación no está disponible.',
                ]);
            }

            $overlap = Booking::where('room_id', $room->id)
                ->whereIn('status', [
                    Booking::STATUS_PENDING,
                    Booking::STATUS_CONFIRMED,
                ])
                ->where('check_in_date', '<', $validated['check_out_date'])
                ->where('check_out_date', '>', $validated['check_in_date'])
                ->exists();

            if ($overlap) {
                throw ValidationException::withMessages([
                    'check_in_date' => 'La habitación ya está reservada para esas fechas.',
                ]);
            }

            $user->bookings()->create([
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
}
