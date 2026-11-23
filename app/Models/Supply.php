<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'item_type',
        'quantity',
        'supplier_id',
        'hotel_need_id',
        'unit_price',
        'status',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    public function hotelNeed(): BelongsTo
    {
        return $this->belongsTo(Supply::class, 'hotel_need_id');
    }

    public function offers(): HasMany
    {
        return $this->hasMany(Supply::class, 'hotel_need_id');
    }
}