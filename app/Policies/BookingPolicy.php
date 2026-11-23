<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\Role;
use App\Models\User;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole(
            Role::ADMINISTRATOR,
            Role::HOTEL_MANAGER,
            Role::RECEPTIONIST,
            Role::CUSTOMER,
        );
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->hasRole(Role::ADMINISTRATOR, Role::HOTEL_MANAGER, Role::RECEPTIONIST)
            || ($user->hasRole(Role::CUSTOMER) && $booking->user_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->hasRole(Role::ADMINISTRATOR, Role::RECEPTIONIST, Role::CUSTOMER);
    }

    public function update(User $user, Booking $booking): bool
    {
        if ($user->hasRole(Role::ADMINISTRATOR, Role::RECEPTIONIST)) {
            return true;
        }

        return $user->hasRole(Role::CUSTOMER)
            && $booking->user_id === $user->id
            && $booking->status !== Booking::STATUS_CANCELLED;
    }
}
