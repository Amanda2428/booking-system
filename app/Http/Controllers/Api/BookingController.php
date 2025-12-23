<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Booking;

class BookingController extends Controller
{
    /**
     * Get all bookings (Admin)
     */
    public function index()
    {
        $bookings = Booking::with(['user', 'room'])->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $bookings
        ], 200);
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
    public function show(string $id)
    {
        $booking = Booking::with(['user', 'room', 'feedback'])->find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $booking
        ], 200);
    }

    /**
     * Update booking (Admin / Student cancel)
     */
    public function update(Request $request, string $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Booking not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'status'     => 'nullable|in:pending,approved,rejected,cancelled',
            'start_time' => 'nullable',
            'end_time'   => 'nullable|after:start_time',
            'date'       => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $booking->update($request->only([
            'status',
            'date',
            'start_time',
            'end_time'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Booking updated successfully',
            'data'    => $booking
        ], 200);
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
}
