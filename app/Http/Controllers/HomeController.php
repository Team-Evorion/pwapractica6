<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featuredRooms = Room::query()
            ->where('is_available', true)
            ->orderByDesc('room_price')
            ->limit(3)
            ->get();

        return view('home', compact('featuredRooms'));
    }
}
