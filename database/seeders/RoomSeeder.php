<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [101, 'Individual', '45.00', 1, 'images/rooms/room-individual.webp'],
            [102, 'Individual', '45.00', 1, 'images/rooms/room-individual.webp'],
            [201, 'Doble', '75.00', 2, 'images/rooms/room-doble.webp'],
            [202, 'Doble', '75.00', 2, 'images/rooms/room-doble.webp'],
            [301, 'Suite', '140.00', 3, 'images/rooms/room-suite.webp'],
            [302, 'Suite', '175.00', 4, 'images/rooms/room-suite.webp'],
            [401, 'Familiar', '190.00', 5, 'images/rooms/room-familiar.webp'],
            [402, 'Familiar', '240.00', 6, 'images/rooms/room-familiar.webp'],
        ];

        foreach ($rooms as [$number, $type, $price, $capacity, $imagePath]) {
            Room::updateOrCreate(
                ['room_number' => $number],
                [
                    'room_type' => $type,
                    'room_price' => $price,
                    'is_available' => true,
                    'description' => "Habitación {$type} preparada para una estancia confortable.",
                    'capacity' => $capacity,
                    'image_path' => $imagePath,
                ],
            );
        }
    }
}
