<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalBookings = Booking::where('user_id', $user->id)->count();
        $activeBookings = Booking::where('user_id', $user->id)
            ->where('status', 'approved')
            ->where('date', '>=', Carbon::today())
            ->count();
        $pendingBookings = Booking::where('user_id', $user->id)
            ->where('status', 'pending')
            ->where('date', '>=', Carbon::today())
            ->count();

        $upcomingBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['approved', 'pending'])
            ->where('date', '>=', Carbon::today())
            ->with('room')
            ->orderBy('date') 
            ->orderBy('start_time')
            ->take(6)
            ->get();

  
        $availableRooms = Room::where('availability_status', 'available')
            ->with('category')
            ->orderBy('name')
            ->take(5)
            ->get();

   
        $recentActivity = collect();

        // Recent bookings
        $recentBookings = Booking::where('user_id', $user->id)
            ->with('room')
            ->latest()
            ->take(3)
            ->get();

        foreach ($recentBookings as $booking) {
            $recentActivity->push([
                'type' => 'booking',
                'message' => 'Booking #' . $booking->id . ' for ' . $booking->room->name,
                'time' => $booking->created_at->diffForHumans(),
            ]);
        }

        if ($recentActivity->isEmpty()) {
            $recentActivity->push([
                'type' => 'welcome',
                'message' => 'Welcome to StudySpace! Book your first room.',
                'time' => 'Just now',
            ]);
        }

        return view('user.dashboard', compact(
            'totalBookings',
            'activeBookings',
            'pendingBookings',
            'upcomingBookings',
            'availableRooms',
            'recentActivity'
        ));
    }
}
