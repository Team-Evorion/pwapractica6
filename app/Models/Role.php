<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    public const ADMINISTRATOR = 'Administrator';

    public const HOTEL_MANAGER = 'Hotel Manager';

    public const RECEPTIONIST = 'Receptionist';

    public const CUSTOMER = 'Customer';

    public const SUPPLIER = 'Supplier';

    protected $fillable = [
        'name',
    ];

    public function getDisplayNameAttribute(): string
    {
        return match ($this->name) {
            self::ADMINISTRATOR => 'Administrador',
            self::HOTEL_MANAGER => 'Gerente del hotel',
            self::RECEPTIONIST => 'Recepcionista',
            self::CUSTOMER => 'Cliente',
            self::SUPPLIER => 'Proveedor',
            default => $this->name,
        };
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
