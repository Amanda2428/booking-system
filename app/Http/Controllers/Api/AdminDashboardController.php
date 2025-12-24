<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Models\Feedback;

class AdminDashboardController extends Controller
{
     public function index()
    {
        $stats = [
            'totalBookings' => Booking::count(),
            'pendingBookings' => Booking::where('status', 'pending')->count(),
            'availableRooms' => Room::where('availability_status', 'available')->count(),
            'totalUsers' => User::count(),
            'todayBookings' => Booking::whereDate('date', today())->count(),
        ];

        $recentBookings = Booking::with(['user', 'room'])
            ->latest()
            ->limit(5)
            ->get();

        $roomStatus = Room::with('category')
            ->orderBy('availability_status')
            ->limit(5)
            ->get();

        return view('dashboard', compact('stats', 'recentBookings', 'roomStatus'));
    }
}