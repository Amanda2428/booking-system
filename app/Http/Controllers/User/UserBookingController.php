<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Log;

class UserBookingController extends Controller
{
    public function index(Request $request)
    {

        $user = Auth::user();

        $query = Booking::with(['room.category'])
            ->where('user_id', $user->id);

        // Apply filters
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('date') && $request->date) {
            $query->where('date', $request->date);
        }

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('room', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            });
        }

        // Sort by date (upcoming first, then past)
        $query->orderByRaw("CASE WHEN date >= CURDATE() THEN 0 ELSE 1 END")
            ->orderBy('date')
            ->orderBy('start_time');

        $bookings = $query->paginate(15);

        // Load feedback for each booking
        $bookings->load('feedback');

        return view('user.bookings', compact('bookings'));
    }

    public function create()
    {
        $rooms = Room::where('availability_status', 'available')
            ->with('category')
            ->orderBy('name')
            ->get();

        return view('user.create-booking', compact('rooms'));
    }

    public function store(Request $request)
    {

        $user = Auth::user();

        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'purpose' => 'nullable|string|max:500',
        ]);

        // Check if room is available
        $room = Room::findOrFail($request->room_id);
        if ($room->availability_status != 'available') {
            return back()
                ->withInput()
                ->withErrors(['room_id' => 'This room is currently unavailable.']);
        }

        // Validate booking duration (max 4 hours)
        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        $duration = $end->diffInHours($start);

        if ($duration > 4) {
            return back()
                ->withInput()
                ->withErrors(['end_time' => 'Booking duration cannot exceed 4 hours.']);
        }

        // Validate booking hours (8 AM to 9 PM)
        if ($start->hour < 8 || $end->hour > 21 || ($end->hour == 21 && $end->minute > 0)) {
            return back()
                ->withInput()
                ->withErrors(['time' => 'Bookings are only allowed between 8:00 AM and 9:00 PM.']);
        }

        // Check for conflicts with approved bookings
        $conflict = Booking::where('room_id', $request->room_id)
            ->where('date', $request->date)
            ->where('status', 'approved')
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                });
            })
            ->exists();

        if ($conflict) {
            return back()
                ->withInput()
                ->withErrors(['time' => 'This room is already booked for the selected time. Please choose a different time.']);
        }

        // Check user's active bookings limit (max 3 pending/approved)
        $activeBookings = Booking::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where('date', '>=', Carbon::today())
            ->count();

        if ($activeBookings >= 3) {
            return back()
                ->withInput()
                ->withErrors(['general' => 'You can have maximum 3 active bookings at a time.']);
        }

        // Create booking
        $booking = Booking::create([
            'user_id' => $user->id,
            'room_id' => $request->room_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'pending',
            'purpose' => $request->purpose,
        ]);

        return redirect()->route('user.bookings')
            ->with('success', 'Booking request submitted successfully. Waiting for admin approval.');
    }

    public function show($id)
    {

        $user = Auth::user();

        $booking = Booking::with(['room.category', 'feedback'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'booking' => $booking
        ]);
    }

    public function cancel($id)
    {

        $user = Auth::user();

        $booking = Booking::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Only allow cancelling pending or approved bookings that haven't started
        if ($booking->status == 'cancelled' || $booking->status == 'rejected') {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be cancelled.'
            ], 422);
        }

        if (
            $booking->date < Carbon::today() ||
            ($booking->date == Carbon::today() && $booking->start_time < Carbon::now()->format('H:i'))
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot cancel booking that has already started.'
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully.'
        ]);
    }

    public function storeFeedback(Request $request, $id)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);

        
        

        if ($booking->feedback()->exists()) {
            return response()->json(['success' => false, 'message' => 'Feedback already exists.'], 400);
        }

        Feedback::create([
            'booking_id' => $booking->id,
            'user_id' => Auth::id(),
            'room_id' => $booking->room_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['success' => true, 'message' => 'Feedback submitted!']);
    }
    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $conflict = Booking::where('room_id', $request->room_id)
            ->where('date', $request->date)
            ->where('status', 'approved')
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                });
            })
            ->exists();

        if ($conflict) {
            // Get conflicting bookings for suggestions
            $conflictingBookings = Booking::where('room_id', $request->room_id)
                ->where('date', $request->date)
                ->where('status', 'approved')
                ->where(function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('start_time', '<', $request->end_time)
                            ->where('end_time', '>', $request->start_time);
                    });
                })
                ->get();

            // Generate suggestions
            $suggestions = $this->generateSuggestions($request, $conflictingBookings);

            return response()->json([
                'available' => false,
                'message' => 'Room is already booked for the selected time.',
                'conflicting_bookings' => $conflictingBookings,
                'suggestions' => $suggestions
            ]);
        }

        return response()->json([
            'available' => true,
            'message' => 'Room is available for the selected time.'
        ]);
    }

    private function generateSuggestions($request, $conflictingBookings)
    {
        $suggestions = [];
        $date = $request->date;

        // Suggest earlier time
        $earlierTime = Carbon::parse($request->start_time)->subHours(2)->format('H:i');
        if ($earlierTime >= '08:00') {
            $suggestions[] = [
                'time' => $earlierTime . ' - ' . $request->start_time,
                'message' => 'Try 2 hours earlier'
            ];
        }

        // Suggest later time
        $laterTime = Carbon::parse($request->end_time)->addHours(2)->format('H:i');
        if ($laterTime <= '21:00') {
            $suggestions[] = [
                'time' => $request->end_time . ' - ' . $laterTime,
                'message' => 'Try 2 hours later'
            ];
        }

        // Suggest different room for same time
        $availableRooms = Room::where('availability_status', 'available')
            ->where('id', '!=', $request->room_id)
            ->whereDoesntHave('bookings', function ($q) use ($date, $request) {
                $q->where('date', $date)
                    ->where('status', 'approved')
                    ->where(function ($query) use ($request) {
                        $query->where(function ($q) use ($request) {
                            $q->where('start_time', '<', $request->end_time)
                                ->where('end_time', '>', $request->start_time);
                        });
                    });
            })
            ->take(2)
            ->get();

        foreach ($availableRooms as $room) {
            $suggestions[] = [
                'time' => $request->start_time . ' - ' . $request->end_time,
                'message' => "Try {$room->name} instead"
            ];
        }

        return $suggestions;
    }
}
