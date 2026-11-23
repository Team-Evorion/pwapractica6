<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'booking_id' => Booking::factory(),
            'recorded_by' => User::factory(),
            'kind' => Payment::KIND_PAYMENT,
            'amount' => fake()->randomFloat(2, 10, 200),
            'method' => fake()->randomElement(Payment::METHODS),
            'reference' => fake()->optional()->bothify('REF-####'),
            'notes' => fake()->optional()->sentence(),
            'paid_at' => now(),
        ];
    }
}
