<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_CANCELLED = 'cancelled';

    public const STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'user_id',
        'room_id',
        'booking_date',
        'check_in_date',
        'check_out_date',
        'status',
        'nightly_rate',
        'total_amount',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'check_in_date' => 'date',
            'check_out_date' => 'date',
            'nightly_rate' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function receivedPayments(): HasMany
    {
        return $this->hasMany(Payment::class)->where('kind', Payment::KIND_PAYMENT);
    }

    public function refunds(): HasMany
    {
        return $this->hasMany(Payment::class)->where('kind', Payment::KIND_REFUND);
    }

    public function nights(): int
    {
        return max(1, $this->check_in_date->diffInDays($this->check_out_date));
    }

    public function totalAmount(): float
    {
        return (float) ($this->total_amount ?? ((float) $this->nightly_rate * $this->nights()));
    }

    public function netPaid(): float
    {
        $received = (float) ($this->received_payments_sum_amount ?? $this->receivedPayments()->sum('amount'));
        $refunded = (float) ($this->refunds_sum_amount ?? $this->refunds()->sum('amount'));

        return max(0, $received - $refunded);
    }

    public function balanceDue(): float
    {
        return max(0, $this->totalAmount() - $this->netPaid());
    }
}
