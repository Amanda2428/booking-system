<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['bookings' => function ($q) {
            $q->where('status', 'approved');
        }]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by verification status (optional)
        if ($request->filled('status')) {
            if ($request->status == 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->status == 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        // Pagination with request parameters preserved
        $users = $query->latest()->paginate(15)->appends($request->all());

        // Dashboard statistics
        $totalUsers    = User::count();
        $totalStudents = User::where('role', 0)->count();
        $totalAdmins   = User::where('role', 1)->count();

        return view('admin.users', compact(
            'users',
            'totalUsers',
            'totalStudents',
            'totalAdmins'
        ));
    }


    public function create()
    {
        return view('admin.users.create');
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'role' => 'required|in:0,1',
                'profile' => 'nullable|image|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'email_verified_at' => $request->role == 1 ? now() : null,
            ];

            if ($request->hasFile('profile')) {
                $data['profile'] = $request->file('profile')->store('profiles', 'public');
            }

            User::create($data);

            return response()->json([
                'success' => true,
                'message' => 'User created successfully'
            ]);
        } catch (\Exception $e) {
            // No logging, just return the error
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }
    public function show(User $user)
    {
        $user->load(['bookings' => function ($q) {
            $q->with('room')->latest()->take(10);
        }, 'feedbacks' => function ($q) {
            $q->with('room')->latest()->take(10);
        }]);

        // Get additional stats
        $user->active_bookings = $user->bookings()
            ->whereIn('status', ['pending', 'approved'])
            ->where('date', '>=', now()->format('Y-m-d'))
            ->count();

        $user->feedbacks_count = $user->feedbacks()->count();
        $user->avg_rating = $user->feedbacks()->avg('rating') ?? 0;

        // Get last activity from sessions table
        $lastSession = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderBy('last_activity', 'desc')
            ->first();

        $user->last_active = $lastSession ? Carbon::createFromTimestamp($lastSession->last_activity) : null;

        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:0,1',
            'password' => 'nullable|min:8|confirmed',
            'profile' => 'nullable|image|max:2048',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($validated['password']);
        }

        if ($request->hasFile('profile')) {
            if ($user->profile) {
                Storage::disk('public')->delete($user->profile);
            }
            $data['profile'] = $request->file('profile')->store('profiles', 'public');
        }

        $user->update($data);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully'
        ]);
    }


    public function destroy(User $user)
    {
        $authUser = Auth::user();

        // Prevent deleting yourself
        if ($user->is($authUser)) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete yourself'
            ], 403);
        }

        // Delete profile image if exists
        if ($user->profile) {
            Storage::disk('public')->delete($user->profile);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }



    public function verify(User $user)
    {
        $user->email_verified_at = now();
        $user->save();

        return response()->json(['success' => true]);
    }

    public function bookings(User $user)
    {
        $bookings = $user->bookings()
            ->with('room.category')
            ->latest()
            ->paginate(15);

        return view('admin.users.bookings', compact('user', 'bookings'));
    }

    public function feedbacks(User $user)
    {
        $feedbacks = $user->feedbacks()
            ->with(['room', 'booking'])
            ->latest()
            ->paginate(15);

        return view('admin.users.feedbacks', compact('user', 'feedbacks'));
    }
}
