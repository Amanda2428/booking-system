<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RoomType;


class RoomTypeController extends Controller
{
    // Display listing of all categories
public function index(Request $request)
{
    // Start query with rooms relationship
    $query = RoomType::with('rooms');

    // Apply search if present
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // Paginate results
    $categories = $query->paginate(9)->appends($request->all());

    // Calculate available rooms and average capacity
    $categories->getCollection()->transform(function ($category) {
        $category->available_rooms = $category->rooms
            ->where('availability_status', 'available')
            ->count();
        $category->avg_capacity = round($category->rooms->avg('capacity') ?? 0, 3);
        return $category;
    });

    return view('admin.categories', compact('categories'));
}


    // Store new category
  public function store(Request $request)
{
    $validator = validator($request->all(), [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
    ]);

    // Validation errors
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Duplicate check
    $exists = RoomType::where('name', $request->name)->exists();

    if ($exists) {
        return response()->json([
            'success' => false,
            'errors' => [
                'name' => ['Category type already exists.']
            ]
        ], 409);
    }

    // Create category
    $category = RoomType::create([
        'name' => $request->name,
        'description' => $request->description,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Category created successfully',
        'category' => $category
    ], 201);
}




    // Show single category
    public function show($id)
    {
        $category = RoomType::with('rooms')->findOrFail($id);
        return response()->json($category);
    }

    // Update category
   public function update(Request $request, $id)
{
    $category = RoomType::findOrFail($id);

    $validator = validator($request->all(), [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string|max:1000',
    ]);

    // Validation errors
    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    // Duplicate name check (ignore current record)
    $exists = RoomType::where('name', $request->name)
        ->where('id', '!=', $category->id)
        ->exists();

    if ($exists) {
        return response()->json([
            'success' => false,
            'errors' => [
                'name' => ['Category type already exists.']
            ]
        ], 409);
    }

    // Update category
    $category->update([
        'name' => $request->name,
        'description' => $request->description,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Category updated successfully',
        'category' => $category
    ], 200);
}


    // Delete category
    public function destroy($id)
    {
        $category = RoomType::findOrFail($id);
        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully'
        ]);
    }
}
