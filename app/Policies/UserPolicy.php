<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(Role::ADMINISTRATOR);
    }

    public function view(User $user, User $target): bool
    {
        return $user->hasRole(Role::ADMINISTRATOR) || $user->id === $target->id;
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMINISTRATOR);
    }

    public function update(User $user, User $target): bool
    {
        return $user->hasRole(Role::ADMINISTRATOR);
    }

    public function assignRole(User $user, User $target): bool
    {
        return $user->hasRole(Role::ADMINISTRATOR);
    }

    public function toggleActive(User $user, User $target): bool
    {
        if (! $user->hasRole(Role::ADMINISTRATOR)) {
            return false;
        }

        // Evita que un administrador se desactive a sí mismo
        return $user->id !== $target->id;
    }

    public function delete(User $user, User $target): bool
    {
        if (! $user->hasRole(Role::ADMINISTRATOR)) {
            return false;
        }

        // Evita auto-eliminación
        return $user->id !== $target->id;
    }
}