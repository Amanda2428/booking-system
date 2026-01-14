<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\Booking;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserRoomController extends Controller
{

    
    public function index(Request $request)
    {
        $query = Room::with(['category', 'feedbacks'])
            ->where('availability_status', 'available');

        // Apply filters
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('capacity') && $request->capacity) {
            if ($request->capacity == '21') {
                $query->where('capacity', '>=', 20);
            } else {
                $query->where('capacity', '>=', $request->capacity);
            }
        }

        if ($request->has('category') && $request->category) {
            $query->where('room_type_id', $request->category);
        }

        // Time availability filter
        if (
            $request->has('date') && $request->date &&
            $request->has('start_time') && $request->start_time &&
            $request->has('end_time') && $request->end_time
        ) {

            $query->whereDoesntHave('bookings', function ($q) use ($request) {
                $q->where('date', $request->date)
                    ->where('status', 'approved')
                    ->where(function ($query) use ($request) {
                        $query->where(function ($q) use ($request) {
                            $q->where('start_time', '<', $request->end_time)
                                ->where('end_time', '>', $request->start_time);
                        });
                    });
            });
        }

        // Calculate statistics for each room
        $rooms = $query->paginate(12)->through(function ($room) use ($request) {
            // Calculate average rating
            $room->avg_rating = $room->feedbacks->avg('rating') ?? 0;
            $room->total_bookings = $room->bookings()->count();

            // Check if room is available today
            $date = $request->date ?? Carbon::today()->format('Y-m-d');
            $room->available_today = !$room->bookings()
                ->where('date', $date)
                ->where('status', 'approved')
                ->where(function ($q) {
                    $q->where('start_time', '<', Carbon::now()->format('H:i'))
                        ->where('end_time', '>', Carbon::now()->format('H:i'));
                })
                ->exists();

            // Calculate availability percentage (simplified)
            $totalBookings = $room->bookings()->count();
            $approvedBookings = $room->bookings()->where('status', 'approved')->count();
            $room->availability_percentage = $totalBookings > 0
                ? round((1 - ($approvedBookings / $totalBookings)) * 100)
                : 100;

            return $room;
        });

        $categories = RoomType::all();

        return view('user.rooms', compact('rooms', 'categories'));
    }

   public function available(Request $request)
{
    $request->validate([
        'date' => 'required|date|after_or_equal:today'
    ]);

    $date = $request->date;
    $startTime = $request->get('start_time'); // Optional: for future use
    $endTime = $request->get('end_time'); // Optional: for future use

    // Get all available rooms
    $rooms = Room::with('category')
        ->where('availability_status', 'available')
        ->orderBy('name')
        ->get();

    // If no time parameters provided, show all rooms (just mark availability status)
    if (!$startTime || !$endTime) {
        $rooms = $rooms->map(function ($room) use ($date) {
            // Get approved bookings for this date
            $approvedBookings = Booking::where('room_id', $room->id)
                ->where('date', $date)
                ->where('status', 'approved')
                ->get();

            // Add booking info to room object
            $room->has_bookings = $approvedBookings->count() > 0;
            $room->approved_bookings = $approvedBookings;
            $room->available_slots = $this->getAvailabilitySlots($room, Carbon::parse($date));
            
            return $room;
        });

        return response()->json([
            'success' => true,
            'date' => $date,
            'rooms' => $rooms
        ]);
    }

    // If time parameters are provided, check for conflicts
    $availableRooms = $rooms->filter(function ($room) use ($date, $startTime, $endTime) {
        // Check for conflicting approved bookings
        $hasConflict = Booking::where('room_id', $room->id)
            ->where('date', $date)
            ->where('status', 'approved')
            ->where(function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                      ->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        return !$hasConflict;
    });

    return response()->json([
        'success' => true,
        'date' => $date,
        'rooms' => $availableRooms->values()
    ]);
}

    public function show($id)
    {
        $room = Room::with(['category', 'feedbacks' => function ($q) {
            $q->with('user')->latest()->take(5);
        }])->findOrFail($id);

        // Calculate statistics
        $room->total_bookings = $room->bookings()->count();
        $room->avg_rating = $room->feedbacks->avg('rating') ?? 0;
        $room->upcoming_bookings = $room->bookings()
            ->where('status', 'approved')
            ->where('date', '>=', Carbon::today())
            ->count();

        // Today's availability slots
        $today = Carbon::today();
        $room->today_availability = $this->getAvailabilitySlots($room, $today);

        // Recent feedbacks
        $room->recent_feedbacks = $room->feedbacks->take(3);

        return response()->json([
            'success' => true,
            'room' => $room
        ]);
    }

    private function getAvailabilitySlots($room, $date)
{
    $slots = [];
    $startHour = 8; // 8 AM
    $endHour = 21;  // 9 PM

    // Get approved bookings for the date
    $bookings = $room->bookings()
        ->where('date', $date->format('Y-m-d'))
        ->where('status', 'approved')
        ->get();

    $currentSlot = null;
    
    for ($hour = $startHour; $hour < $endHour; $hour++) {
        $slotStart = sprintf('%02d:00', $hour);
        $slotEnd = sprintf('%02d:00', $hour + 1);

        // Check if slot is available
        $available = true;
        foreach ($bookings as $booking) {
            if ($this->timeOverlaps($slotStart, $slotEnd, $booking->start_time, $booking->end_time)) {
                $available = false;
                break;
            }
        }

        // Group consecutive available slots
        if ($available) {
            if ($currentSlot === null) {
                $currentSlot = [
                    'start' => $slotStart,
                    'end' => $slotEnd,
                    'duration' => 1
                ];
            } else {
                // Extend the current slot
                $currentSlot['end'] = $slotEnd;
                $currentSlot['duration']++;
            }
        } else {
            // Save the current slot if it exists
            if ($currentSlot !== null) {
                $slots[] = [
                    'time' => date('h:i A', strtotime($currentSlot['start'])) . ' - ' . 
                             date('h:i A', strtotime($currentSlot['end'])),
                    'start' => $currentSlot['start'],
                    'end' => $currentSlot['end'],
                    'duration' => $currentSlot['duration'],
                    'available' => true
                ];
                $currentSlot = null;
            }
        
        }
    }

    // Save the last slot if it exists
    if ($currentSlot !== null) {
        $slots[] = [
            'time' => date('h:i A', strtotime($currentSlot['start'])) . ' - ' . 
                     date('h:i A', strtotime($currentSlot['end'])),
            'start' => $currentSlot['start'],
            'end' => $currentSlot['end'],
            'duration' => $currentSlot['duration'],
            'available' => true
        ];
    }

    return $slots;
}

    private function timeOverlaps($start1, $end1, $start2, $end2)
    {
        return $start1 < $end2 && $end1 > $start2;
    }
}
