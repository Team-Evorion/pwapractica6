<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supply extends Model
{
    use HasFactory;

    public const TYPE_NEED = 'need';

    public const TYPE_OFFER = 'offer';

    public const TYPE_SUPPLY = 'supply';

    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    public const STATUS_OFFERED = 'offered';

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
