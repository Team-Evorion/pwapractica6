<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Role;
use App\Models\Room;
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

        $hotelSummary = [
            'total_rooms' => Room::count(),
            'available_rooms' => Room::where('is_available', true)->count(),
            'maintenance_rooms' => Room::where('is_available', false)->count(),
            'active_reservations' => Booking::whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED,
            ])->count(),
            'checkins_today' => Booking::where('status', Booking::STATUS_CONFIRMED)
                ->whereDate('check_in_date', today())
                ->count(),
            'checkouts_today' => Booking::where('status', Booking::STATUS_CONFIRMED)
                ->whereDate('check_out_date', today())
                ->count(),
        ];

        return view('admin.dashboard', compact(
            'totalUsers', 'activeUsers', 'inactiveUsers', 'totalSupplies',
            'roleDistribution', 'recentUsers', 'hotelSummary'
        ));
    }
}
