<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Supply;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(): View
    {
        Gate::authorize('viewAny', User::class);

        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $totalSupplies = Supply::count();

        $roleDistribution = Role::withCount('users')->orderBy('name')->get();
        $recentUsers = User::with('role')->orderByDesc('created_at')->limit(5)->get();

        // Datos simulados temporales hasta integrar Habitaciones (José Rosado) y Reservaciones (Terry Ponce)
        $mockHotelSummary = [
            'is_simulated' => true,
            'total_rooms' => 48,
            'available_rooms' => 31,
            'occupied_rooms' => 14,
            'maintenance_rooms' => 3,
            'active_reservations' => 19,
            'checkins_today' => 7,
            'checkouts_today' => 5,
            'estimated_daily_revenue' => 2840.00,
        ];

        return view('admin.dashboard', compact(
            'totalUsers', 'activeUsers', 'inactiveUsers', 'totalSupplies',
            'roleDistribution', 'recentUsers', 'mockHotelSummary'
        ));
    }
}