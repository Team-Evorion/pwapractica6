<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\Room;
use App\Models\User;

class RoomPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole(Role::ADMINISTRATOR) ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::HOTEL_MANAGER);
    }

    public function view(User $user, Room $room): bool
    {
        return $user->hasRole(
            Role::HOTEL_MANAGER,
            Role::RECEPTIONIST,
            Role::CUSTOMER,
        );
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::HOTEL_MANAGER);
    }

    public function update(User $user, Room $room): bool
    {
        return $user->hasRole(Role::HOTEL_MANAGER);
    }

    public function delete(User $user, Room $room): bool
    {
        return $user->hasRole(Role::HOTEL_MANAGER);
    }
}
