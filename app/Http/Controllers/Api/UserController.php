<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(User::latest()->get());
    }

    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name'     => 'required|string|max:255',
                'email'    => 'required|email|unique:users,email',
                'password' => 'required|min:6',
                'role'     => 'required|in:student,admin'
            ]);

            $user = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => $request->role,
            ]);

            return response()->json([
                'message' => 'User created successfully',
                'user' => $user
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {

            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user->fresh()
        ]);
    }


    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        return response()->json(['message' => 'User deleted']);
    }
}
