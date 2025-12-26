<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use App\Models\Feedback;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
     public function index()
    {
        // Booking Statistics
        $totalBookings = Booking::count();
        $pendingBookings = Booking::where('status', 'pending')->count();

        // Room Statistics
        $totalRooms = Room::count();
        $availableRooms = Room::where('availability_status', 'available')->count();
        $avgCapacity = Room::avg('capacity') ?? 0;

        // User Statistics
        $totalUsers = User::count();
        // Active users: last 24 hours based on updated_at
        $activeUsers = User::where('updated_at', '>=', now()->subDay())->count();

        // Recent Bookings (latest 5)
        $recentBookings = Booking::with(['user', 'room'])
            ->latest()
            ->take(4)
            ->get();

        // Room Utilization (this week's bookings per room)
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfWeek = Carbon::now()->endOfWeek();

        $roomUtilization = Room::withCount(['bookings' => function($query) use ($startOfWeek, $endOfWeek) {
            $query->whereBetween('date', [$startOfWeek, $endOfWeek])
                  ->where('status', 'approved');
        }])->orderBy('bookings_count', 'desc')
           ->take(4)
           ->get()
           ->map(function($room) {
               // utilization = (bookings this week / 7 days) * 100
               $room->utilization = min(100, ($room->bookings_count * 100) / 7);
               return $room;
           });

        return view('dashboard', compact(
            'totalBookings',
            'pendingBookings',
            'totalRooms',
            'availableRooms',
            'avgCapacity',
            'totalUsers',
            'activeUsers',
            'recentBookings',
            'roomUtilization'
        ));
    }
}