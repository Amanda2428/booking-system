<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;

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
     *  Store new booking (Student)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'    => 'required|exists:users,id',
            'room_id'    => 'required|exists:rooms,id',
            'date'       => 'required|date',
            'start_time' => 'required',
            'end_time'   => 'required|after:start_time',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        /**
         * ❌ Prevent conflict booking
         */
        $conflict = Booking::where('room_id', $request->room_id)
            ->where('date', $request->date)
            ->where(function ($query) use ($request) {
                $query->whereBetween('start_time', [$request->start_time, $request->end_time])
                    ->orWhereBetween('end_time', [$request->start_time, $request->end_time])
                    ->orWhere(function ($q) use ($request) {
                        $q->where('start_time', '<=', $request->start_time)
                            ->where('end_time', '>=', $request->end_time);
                    });
            })
            ->exists();

        if ($conflict) {
            return response()->json([
                'success' => false,
                'message' => 'This room is already booked for the selected time'
            ], 409);
        }

        $booking = Booking::create([
            'user_id'    => $request->user_id,
            'room_id'    => $request->room_id,
            'date'       => $request->date,
            'start_time' => $request->start_time,
            'end_time'   => $request->end_time,
            'status'     => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data'    => $booking
        ], 201);
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
     public function update(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,rejected,cancelled'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $booking->status = $request->status;
        $booking->save();

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data' => $booking
        ]);
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
}
