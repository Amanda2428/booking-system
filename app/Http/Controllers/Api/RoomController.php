<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Room::with('category')->get());
    }


    // 2. Show a single room
    public function show($id)
    {
        try {
            $room = Room::with('category')->findOrFail($id);
            return response()->json($room);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Room not found'
            ], 404);
        }
    }

    // 3. Create a new room
    public function store(Request $request)
    {
        try {
            $request->validate([            
                'name' => 'required|string|max:255|unique:rooms,name',
                'capacity' => 'required|integer|min:1',
                'location' => 'required|string|max:255',
                'description' => 'nullable|string',
                'availability_status' => 'required|in:available,unavailable',
                'room_type_id' => 'required|exists:room_categories,id',
            ]);

            $room = Room::create($request->all());

            return response()->json([
                'message' => 'Room created successfully',
                'room' => $room
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        }
    }

    // 4. Update a room
     public function update(Request $request, $id)
    {
        $room = Room::find($id);

        if (! $room) {
            return response()->json(['status' => 'error', 'message' => 'Room not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'name'             => 'nullable|string|max:255',
            'capacity'         => 'nullable|integer|min:1',
            'location'         => 'nullable|string|max:255',
            'description'      => 'nullable|string',
            'availability_status' => 'nullable|in:available,unavailable',
            'room_type_id'     => 'nullable|exists:room_categories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 400);
        }

        $room->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Room updated successfully',
            'data' => $room
        ], 200);
    }

    // 5. Delete a room
    public function destroy($id)
    {
        try {
            $room = Room::findOrFail($id);
            $room->delete();

            return response()->json([
                'message' => 'Room deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Room not found'
            ], 404);
        }
    }

    // 6. Search rooms
    public function search(Request $request)
    {
        $query = Room::query();

        if ($request->has('name')) {
            $query->where('name', 'LIKE', "%{$request->name}%");
        }

        if ($request->has('location')) {
            $query->where('location', 'LIKE', "%{$request->location}%");
        }

        if ($request->has('capacity')) {
            $query->where('capacity', '>=', $request->capacity);
        }

        if ($request->has('status')) {
            $query->where('availability_status', $request->status);
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $rooms = $query->get();

        if ($rooms->isEmpty()) {
            return response()->json([
                'message' => 'No rooms match your search criteria'
            ], 404);
        }

        return response()->json($rooms);
    }

    public function index2(Request $request)
    {
        try {
            $query = Room::query();

            // Search Filters
            if ($request->has('search')) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%$search%")
                        ->orWhere('location', 'LIKE', "%$search%")
                        ->orWhere('description', 'LIKE', "%$search%");
                });
            }

            if ($request->has('capacity')) {
                $query->where('capacity', '>=', (int)$request->capacity);
            }

            if ($request->has('availability_status')) {
                $query->where('availability_status', $request->availability_status);
            }

            $rooms = $query->get();

            return response()->json($rooms, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Something went wrong',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
