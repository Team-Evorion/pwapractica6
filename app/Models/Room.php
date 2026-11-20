<?php

namespace App\Models;

use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_type',
        'room_price',
        'is_available',
        'description',
        'capacity',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'room_number' => 'integer',
            'room_price' => 'decimal:2',
            'is_available' => 'boolean',
            'capacity' => 'integer',
        ];
    }
}
