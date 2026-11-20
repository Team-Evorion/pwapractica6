<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        $roomTypes = [
            'Individual',
            'Doble',
            'Suite',
            'Familiar',
        ];
        $roomType = fake()->randomElement($roomTypes);
        $images = [
            'Individual' => 'images/rooms/room-individual.webp',
            'Doble' => 'images/rooms/room-doble.webp',
            'Suite' => 'images/rooms/room-suite.webp',
            'Familiar' => 'images/rooms/room-familiar.webp',
        ];

        return [
            'room_number' => fake()->unique()->numberBetween(100, 999),
            'room_type' => $roomType,
            'room_price' => fake()->randomFloat(2, 35, 350),
            'is_available' => true,
            'description' => fake()->sentence(12),
            'capacity' => fake()->numberBetween(1, 6),
            'image_path' => $images[$roomType],
        ];
    }

    public function unavailable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_available' => false,
        ]);
    }
}
