<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Role;
use App\Models\Room;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user()->loadMissing('role');
        $bookingQuery = Booking::query();

        if ($user->hasRole(Role::CUSTOMER)) {
            $bookingQuery->where('user_id', $user->id);
        }

        $activeBookings = (clone $bookingQuery)
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->count();

        $nextBooking = (clone $bookingQuery)
            ->with('room')
            ->whereIn('status', [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])
            ->whereDate('check_in_date', '>=', today())
            ->orderBy('check_in_date')
            ->first();

        return view('dashboard', [
            'user' => $user,
            'totalRooms' => Room::count(),
            'availableRooms' => Room::where('is_available', true)->count(),
            'activeBookings' => $activeBookings,
            'nextBooking' => $nextBooking,
            'openNeeds' => Supply::where('item_type', 'need')->where('status', 'open')->count(),
            'supplierOffers' => $user->hasRole(Role::SUPPLIER)
                ? Supply::where('supplier_id', $user->id)->where('item_type', 'offer')->count()
                : 0,
        ]);
    }
}
