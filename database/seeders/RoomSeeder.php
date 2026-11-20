<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [101, 'Individual', '45.00', 1],
            [102, 'Individual', '45.00', 1],
            [201, 'Doble', '75.00', 2],
            [202, 'Doble', '75.00', 2],
            [301, 'Suite', '140.00', 3],
            [302, 'Suite', '165.00', 4],
            [401, 'Familiar', '190.00', 5],
            [402, 'Familiar', '210.00', 6],
        ];

        foreach ($rooms as [$number, $type, $price, $capacity]) {
            Room::updateOrCreate(
                ['room_number' => $number],
                [
                    'room_type' => $type,
                    'room_price' => $price,
                    'is_available' => true,
                    'description' => "Habitación {$type} preparada para una estancia confortable.",
                    'capacity' => $capacity,
                    'image_path' => null,
                ],
            );
        }
    }
}
