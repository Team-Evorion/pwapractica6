<?php

namespace App\Models;

use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory;

    public const TYPES = [
        'Individual',
        'Doble',
        'Suite',
        'Familiar',
    ];

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

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeAvailableBetween(Builder $query, string $checkIn, string $checkOut): Builder
    {
        return $query
            ->where('is_available', true)
            ->whereDoesntHave('bookings', function (Builder $bookingQuery) use ($checkIn, $checkOut): void {
                $bookingQuery
                    ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
                    ->where('check_in_date', '<', $checkOut)
                    ->where('check_out_date', '>', $checkIn);
            });
    }
}
