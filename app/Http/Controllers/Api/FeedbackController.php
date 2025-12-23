<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    /**
     * Listing all feedbacks
     */
    public function index()
    {
        $feedbacks = Feedback::with(['user', 'room', 'booking'])->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $feedbacks
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|exists:users,id',
                'room_id' => 'required|exists:rooms,id',
                'booking_id' => 'required|exists:bookings,id',
                'rating' => 'required|integer|min:1|max:5',
                'comments' => 'nullable|string',
            ]);

            $feedback = Feedback::create($request->all());

            return response()->json([
                'success' => "Feedback submitted successfully",
                'data' => $feedback
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors()
            ], 422);
        }
    }

    /**
     * Searching for a specific feedback by ID
     */
    public function show(string $id)
    {
        $feedback = Feedback::with(['user', 'room', 'booking'])->find($id);

        if (!$feedback) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $feedback
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $feedback = Feedback::find($id);

        if (!$feedback) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'admin_reply'  => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors'  => $validator->errors()
            ], 422);
        }

        $feedback->update($request->only([
            'admin_reply'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Feedback updated successfully',
            'data'    => $feedback
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $feedback = Feedback::find($id);

        if (!$feedback) {
            return response()->json([
                'success' => false,
                'message' => 'Feedback not found'
            ], 404);
        }

        $feedback->delete();

        return response()->json([
            'success' => true,
            'message' => 'Feedback deleted successfully'
        ], 200);
    }
}
