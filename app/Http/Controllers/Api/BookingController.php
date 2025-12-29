<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Get all bookings (Admin)
     */
    public function index(Request $request)
    {
        $query = Booking::with(['user', 'room'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }

        // ✅ USE paginate()
        $bookings = $query->paginate(10)->withQueryString();

        // Status counts
        $pendingCount   = Booking::where('status', 'pending')->count();
        $approvedCount  = Booking::where('status', 'approved')->count();
        $rejectedCount  = Booking::where('status', 'rejected')->count();
        $cancelledCount = Booking::where('status', 'cancelled')->count();

        return view('admin.bookings', compact(
            'bookings',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'cancelledCount'
        ));
    }
    /**
     *  Store new booking (Admin)
     */
    // public function create()
    // {
    //     // Get all users (or only students if you prefer)
    //     $users = User::orderBy('name')->get();

    //     // Get available rooms
    //     $rooms = Room::with('category')
    //         ->where('availability_status', 'available')
    //         ->orderBy('name')
    //         ->get();

    //     return view('admin.bookings.create', compact('users', 'rooms'));
    // }
    /**
     *  Store new booking (Student)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:pending,approved,rejected,cancelled',
            'purpose' => 'nullable|string|max:500',
        ]);

        // Check for booking conflicts with APPROVED bookings
        $conflict = Booking::where('room_id', $request->room_id)
            ->where('date', $request->date)
            ->where('status', 'approved') // Only check approved bookings
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    // Check if new booking starts during an existing booking
                    $q->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                });
            })
            ->exists();

        if ($conflict && $request->status == 'approved') {
            // Get conflicting bookings to show details
            $conflictingBookings = Booking::where('room_id', $request->room_id)
                ->where('date', $request->date)
                ->where('status', 'approved')
                ->where(function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('start_time', '<', $request->end_time)
                            ->where('end_time', '>', $request->start_time);
                    });
                })
                ->with('user')
                ->get();

            return back()
                ->withInput()
                ->withErrors([
                    'time' => 'Cannot create approved booking: Room is already booked for the selected time.',
                    'conflicts' => $conflictingBookings->toArray() // Convert to array
                ]);
        }

        // Additional validation: If approving a booking, check for conflicts
        if ($request->status == 'approved') {
            // Check if this approval would conflict with any pending or approved bookings
            $approvalConflict = Booking::where('room_id', $request->room_id)
                ->where('date', $request->date)
                ->whereIn('status', ['pending', 'approved'])
                ->where(function ($query) use ($request) {
                    $query->where(function ($q) use ($request) {
                        $q->where('start_time', '<', $request->end_time)
                            ->where('end_time', '>', $request->start_time);
                    });
                })
                ->exists();

            if ($approvalConflict) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'time' => 'Cannot approve this booking because it conflicts with another pending or approved booking.'
                    ]);
            }
        }

        // Validate booking duration (max 4 hours)
        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        $duration = $end->diffInHours($start);

        if ($duration > 4) {
            return back()
                ->withInput()
                ->withErrors([
                    'end_time' => 'Booking duration cannot exceed 4 hours.'
                ]);
        }

        // Validate booking hours (8 AM to 9 PM)
        if ($start->hour < 8 || $end->hour > 21 || ($end->hour == 21 && $end->minute > 0)) {
            return back()
                ->withInput()
                ->withErrors([
                    'time' => 'Bookings are only allowed between 8:00 AM and 9:00 PM.'
                ]);
        }

        $booking = Booking::create([
            'user_id' => $request->user_id,
            'room_id' => $request->room_id,
            'date' => $request->date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => $request->status,
            'purpose' => $request->purpose,
        ]);

        return redirect()->route('admin.bookings')
            ->with('success', 'Booking created successfully.');
    }


    /**
     * Show single booking
     */
    public function show($id)
    {
        $booking = Booking::with(['room', 'user', 'feedback'])->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $booking
        ]);
    }

    /**
     * Update booking (Admin / Student cancel)
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'room_id' => 'sometimes|exists:rooms,id',
            'date' => 'sometimes|date',
            'start_time' => 'sometimes|date_format:H:i',
            'end_time' => 'sometimes|date_format:H:i|after:start_time',
            'status' => 'sometimes|in:pending,approved,rejected,cancelled',
            'purpose' => 'nullable|string|max:500',
        ]);

        // If changing to approved status or changing time/date, check for conflicts
        if (($request->has('status') && $request->status == 'approved') ||
            $request->hasAny(['room_id', 'date', 'start_time', 'end_time'])
        ) {

            $roomId = $request->room_id ?? $booking->room_id;
            $date = $request->date ?? $booking->date;
            $startTime = $request->start_time ?? $booking->start_time;
            $endTime = $request->end_time ?? $booking->end_time;

            $conflict = Booking::where('room_id', $roomId)
                ->where('date', $date)
                ->where('id', '!=', $booking->id)
                ->where('status', 'approved')
                ->where(function ($query) use ($startTime, $endTime) {
                    $query->where(function ($q) use ($startTime, $endTime) {
                        $q->where('start_time', '<', $endTime)
                            ->where('end_time', '>', $startTime);
                    });
                })
                ->exists();

            if ($conflict) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'time' => 'Cannot update booking due to time conflict with another approved booking.'
                    ]);
            }
        }

        $booking->update($validated);

        return redirect()->route('admin.bookings')
            ->with('success', 'Booking updated successfully.');
    }

    /**
     * Delete booking
     */
    public function destroy($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        $booking->delete();

        return response()->json([
            'success' => true,
            'message' => 'Booking deleted successfully'
        ], 200);
    }


    public function search(Request $request)
    {
        $request->validate([
            'q' => 'nullable|string|max:255'
        ]);

        $q = $request->q;

        $bookings = Booking::with(['user', 'room'])
            ->where(function ($query) use ($q) {
                $query->where('id', 'like', "%{$q}%")
                    ->orWhere('status', 'like', "%{$q}%")
                    ->orWhereHas('user', function ($q2) use ($q) {
                        $q2->where('name', 'like', "%{$q}%")
                            ->orWhere('email', 'like', "%{$q}%");
                    })
                    ->orWhereHas('room', function ($q2) use ($q) {
                        $q2->where('name', 'like', "%{$q}%");
                    });
            })
            ->paginate(10);

        return view('admin.bookings', [
            'bookings' => $bookings,
            'searchTerm' => $q
        ]);
    }



    public function adminDashboard()
    {
        // Get statistics
        $stats = [
            'totalBookings' => Booking::count(),
            'pendingBookings' => Booking::where('status', 'pending')->count(),
            'availableRooms' => Room::where('availability_status', 'available')->count(),
            'totalUsers' => User::count(),
            'todayBookings' => Booking::whereDate('date', today())->count(),
        ];

        // Get recent bookings
        $recentBookings = Booking::with(['user', 'room'])
            ->latest()
            ->limit(5)
            ->get();

        $roomStatus = Room::with('category')
            ->orderBy('availability_status')
            ->limit(5)
            ->get();


        // Share data with all admin views (for sidebar stats)
        view()->share([
            'pendingBookingsCount' => $stats['pendingBookings'],
            'availableRoomsCount' => $stats['availableRooms'],
            'todayBookingsCount' => $stats['todayBookings'],
        ]);

        return view('admin.dashboard', compact('stats', 'recentBookings', 'roomStatus'));
    }


    public function export()
    {
        $bookings = Booking::with(['user', 'room'])->get();

        if ($bookings->isEmpty()) {
            return redirect()->back()->with('error', 'No bookings found to export.');
        }

        $csvHeader = ['ID', 'User Name', 'User Email', 'Room', 'Date', 'Start Time', 'End Time', 'Status'];

        $callback = function () use ($bookings, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);

            foreach ($bookings as $b) {
                fputcsv($file, [
                    $b->id,
                    $b->user->name,
                    $b->user->email,
                    $b->room->name,
                    $b->date,
                    $b->start_time,
                    $b->end_time,
                    $b->status,
                ]);
            }

            fclose($file);
        };

        $fileName = 'bookings_' . now()->format('Ymd_His') . '.csv';

        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename={$fileName}"
        ]);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'room_id' => 'required|exists:rooms,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        // Check for conflicts with approved bookings only
        $conflictingBookings = Booking::where('room_id', $request->room_id)
            ->where('date', $request->date)
            ->where('status', 'approved') // Only check approved bookings
            ->where(function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                        ->where('end_time', '>', $request->start_time);
                });
            })
            ->with('user')
            ->get();

        $available = $conflictingBookings->isEmpty();

        return response()->json([
            'available' => $available,
            'message' => $available
                ? 'Room is available for the selected time.'
                : 'Room is already booked for this time.',
            'conflicting_bookings' => $conflictingBookings,
            'room' => Room::find($request->room_id)
        ]);
    }
    public function updateStatus(Request $request, Booking $booking)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected,cancelled',
        ]);

        // If trying to approve, check for conflicts
        if ($request->status == 'approved') {
            $conflict = Booking::where('room_id', $booking->room_id)
                ->where('date', $booking->date)
                ->where('id', '!=', $booking->id)
                ->where('status', 'approved') // Only check approved bookings
                ->where(function ($query) use ($booking) {
                    $query->where(function ($q) use ($booking) {
                        $q->where('start_time', '<', $booking->end_time)
                            ->where('end_time', '>', $booking->start_time);
                    });
                })
                ->exists();

            if ($conflict) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot approve booking due to time conflict with another approved booking.'
                ], 422);
            }
        }

        $booking->update(['status' => $request->status]);

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated successfully.',
            'booking' => $booking
        ]);
    }
}
