<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\Supply;
use App\Models\User;

class SupplyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::SUPPLIER, Role::ADMINISTRATOR, Role::HOTEL_MANAGER);
    }

    public function view(User $user, Supply $supply): bool
    {
        if ($user->hasRole(Role::ADMINISTRATOR, Role::HOTEL_MANAGER)) {
            return true;
        }

        return $user->hasRole(Role::SUPPLIER) && (int) $supply->supplier_id === (int) $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::SUPPLIER);
    }

    public function update(User $user, Supply $supply): bool
    {
        return $user->hasRole(Role::SUPPLIER) && (int) $supply->supplier_id === (int) $user->id;
    }

    public function delete(User $user, Supply $supply): bool
    {
        return $user->hasRole(Role::SUPPLIER) && (int) $supply->supplier_id === (int) $user->id;
    }
}
