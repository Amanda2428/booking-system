<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Feedback;
use App\Models\RoomType;

class WelcomeController extends Controller
{
     public function index()
    {
        // Get statistics for public display
        $stats = [
            'totalRooms' => Room::count(),
            'totalBookings' => Booking::count(),
            'activeUsers' => User::where('role', 0)->count(),
            'approvedBookings' => Booking::where('status', 'approved')->count(),
        ];
        
        // Get featured rooms
        $featuredRooms = Room::with('category')
            ->where('availability_status', 'available')
            ->inRandomOrder()
            ->take(3)
            ->get();
        
        return view('welcome', array_merge($stats, ['featuredRooms' => $featuredRooms]));
    }

public function feedback()
{
   $feedbacks = Feedback::with(['user', 'room'])
    ->orderBy('rating', 'desc') 
    ->orderBy('created_at', 'desc') 
    ->paginate(10);
    
    $totalFeedbacks = $feedbacks->count();
    $averageRating = $totalFeedbacks > 0 ? $feedbacks->avg('rating') : 0;
    $feedbackWithReply = $feedbacks->whereNotNull('admin_reply')->count();
    
    return view('feedback', compact('feedbacks', 'totalFeedbacks', 'averageRating', 'feedbackWithReply'));
}
public function roomTypesShow(){
    $roomTypes = RoomType::withCount('rooms')
            ->orderBy('name', 'asc')
            ->get();
    return view('room-types', compact('roomTypes'));
}
}