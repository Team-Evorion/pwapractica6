<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $checkInDate = now()->addDays(fake()->numberBetween(2, 20))->startOfDay();
        $checkOutDate = $checkInDate->copy()->addDays(fake()->numberBetween(1, 7));
        $nightlyRate = fake()->randomFloat(2, 50, 300);

        return [
            'user_id' => User::factory(),
            'room_id' => Room::factory(),
            'booking_date' => now()->toDateString(),
            'check_in_date' => $checkInDate->toDateString(),
            'check_out_date' => $checkOutDate->toDateString(),
            'status' => Booking::STATUS_PENDING,
            'nightly_rate' => $nightlyRate,
            'total_amount' => $nightlyRate * $checkInDate->diffInDays($checkOutDate),
        ];
    }
}
