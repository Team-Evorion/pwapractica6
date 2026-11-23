<?php

namespace App\Providers;

use App\Models\Room;
use App\Models\Supply;
use App\Models\User;
use App\Policies\RoomPolicy;
use App\Policies\SupplyPolicy;
use App\Policies\UserPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Room::class, RoomPolicy::class);
        Paginator::useBootstrapFive();
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Supply::class, SupplyPolicy::class);
    }
}
