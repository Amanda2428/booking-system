<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomType;

class RoomTypeController extends Controller
{
    /**
     * Display listing of all rooms
     */
    public function index()
    {
        $roomTypes = RoomType::with('rooms')->get();
        return response()->json($roomTypes);
    }

    /**
     * Storing of room data
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:room_categories,name',
            'description' => 'nullable|string|max:1000',
        ]);

        $roomType = RoomType::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Room type created successfully',
            'roomType' => $roomType
        ], 201);
    }

    /**
     * Display the single room 
     */
    public function show(string $id)
    {
        $roomType = RoomType::with('rooms')->findOrFail($id);
        return response()->json($roomType);
    }

    /**
     * Updating the room type data
     */
    public function update(Request $request, string $id)
    {
        $roomType = RoomType::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:room_categories,name,' . $roomType->id,
            'description' => 'nullable|string|max:1000',
        ]);

        $roomType->name = $request->input('name');
        $roomType -> description = $request ->input('description');
        $roomType -> save();

        return response()->json([
            'message' => 'Room type updated successfully',
            'roomType' => $roomType
        ]);
    }

    /**
     * Deleting the room types data
     */
    public function destroy(string $id)
    {
        $roomType = RoomType::findOrFail($id);
        $roomType->delete();

        return response()->json([
            'message' => 'Room type deleted successfully'
        ]);
    }
}
