<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Room;
use App\Models\RoomType;

class RoomController extends Controller
{
    public function index(Request $request)
    {
        $query = Room::with('category');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $rooms = $query->latest()->paginate(12);

        $totalRooms = Room::count();
        $availableRooms = Room::where('availability_status', 'available')->count();
        $avgCapacity = Room::avg('capacity') ?? 0;

        $categories = RoomType::all();

        return view('admin.rooms', compact(
            'rooms',
            'totalRooms',
            'availableRooms',
            'avgCapacity',
            'categories'
        ));
    }

    public function create()
    {
        $categories = RoomType::all();
        return view('admin.rooms.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name',
            'capacity' => 'required|integer|min:1|max:100',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'availability_status' => 'required|in:available,unavailable',
            'room_type_id' => 'required|exists:room_categories,id',
        ]);

        Room::create($validated);

        return redirect()
            ->route('admin.rooms')
            ->with('success', 'Room created successfully.');
    }

    // ✅ THIS FIXES JSON ERROR
    public function show(Room $room)
    {
        $room->load([
            'category',
            'bookings' => function ($q) {
                $q->where('status', 'approved')->latest()->take(10);
            }
        ]);

        return response()->json($room);
    }

    public function edit(Room $room)
    {
        $categories = RoomType::all();
        return view('admin.rooms.edit', compact('room', 'categories'));
    }

    public function update(Request $request, Room $room)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:rooms,name,' . $room->id,
            'capacity' => 'required|integer|min:1|max:100',
            'location' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'availability_status' => 'required|in:available,unavailable',
            'room_type_id' => 'required|exists:room_categories,id',
        ]);

        $room->update($validated);

        return redirect()
            ->route('admin.rooms')
            ->with('success', 'Room updated successfully.');
    }

public function destroy(Room $room)
{
    try {
        $room->delete();

        // ✅ Return JSON for fetch()
        return response()->json([
            'success' => true,
            'message' => 'Room deleted successfully.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete room.'
        ], 500);
    }
}
}
