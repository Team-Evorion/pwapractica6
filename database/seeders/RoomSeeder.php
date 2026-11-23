<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [101, 'Individual', '38.00', 1, true, 'Individual económica con cama sencilla, escritorio y Wi-Fi.', 'images/rooms/room-individual.webp'],
            [102, 'Individual', '38.00', 1, true, 'Individual económica, práctica y tranquila para estadías cortas.', 'images/rooms/room-individual.webp'],
            [103, 'Individual', '38.00', 1, true, 'Individual económica con espacio de trabajo y baño privado.', 'images/rooms/room-individual.webp'],
            [104, 'Individual', '48.00', 1, true, 'Individual superior con cama amplia, minibar y mejor ubicación.', 'images/rooms/room-individual.webp'],
            [105, 'Individual', '48.00', 1, false, 'Individual superior temporalmente en mantenimiento preventivo.', 'images/rooms/room-individual.webp'],
            [201, 'Doble', '60.00', 2, true, 'Doble estándar con cama matrimonial, televisión y escritorio.', 'images/rooms/room-doble.webp'],
            [202, 'Doble', '60.00', 2, true, 'Doble estándar para parejas o viajes de trabajo.', 'images/rooms/room-doble.webp'],
            [203, 'Doble', '60.00', 2, true, 'Doble estándar con baño privado y climatización.', 'images/rooms/room-doble.webp'],
            [204, 'Doble', '60.00', 2, true, 'Doble estándar con dos camas y Wi-Fi incluido.', 'images/rooms/room-doble.webp'],
            [205, 'Doble', '75.00', 2, true, 'Doble superior con cama queen, minibar y vista preferencial.', 'images/rooms/room-doble.webp'],
            [206, 'Doble', '75.00', 2, true, 'Doble superior con mobiliario renovado y mayor espacio.', 'images/rooms/room-doble.webp'],
            [301, 'Familiar', '95.00', 4, true, 'Familiar con dos camas, espacio para cuatro huéspedes y comedor.', 'images/rooms/room-familiar.webp'],
            [302, 'Familiar', '95.00', 4, true, 'Familiar amplia con almacenamiento y zona de descanso.', 'images/rooms/room-familiar.webp'],
            [303, 'Familiar', '95.00', 4, false, 'Familiar temporalmente fuera de servicio por mantenimiento.', 'images/rooms/room-familiar.webp'],
            [401, 'Suite', '125.00', 2, true, 'Suite ejecutiva con cama king, sala privada y desayuno incluido.', 'images/rooms/room-suite.webp'],
            [402, 'Suite', '145.00', 5, true, 'Suite familiar de dos ambientes con capacidad para cinco huéspedes.', 'images/rooms/room-suite.webp'],
        ];

        foreach ($rooms as [$number, $type, $price, $capacity, $isAvailable, $description, $imagePath]) {
            Room::updateOrCreate(
                ['room_number' => $number],
                [
                    'room_type' => $type,
                    'room_price' => $price,
                    'is_available' => $isAvailable,
                    'description' => $description,
                    'capacity' => $capacity,
                    'image_path' => $imagePath,
                ],
            );
        }
    }
}
