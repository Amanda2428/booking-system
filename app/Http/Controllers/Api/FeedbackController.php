<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\Validator;
use App\Models\Room;;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $query = Feedback::with(['user', 'room', 'booking']);

        // Apply filters
        if ($request->has('rating') && $request->rating != '') {
            $query->where('rating', $request->rating);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('created_at', $request->date);
        }

        if ($request->has('unreplied') && $request->unreplied == '1') {
            $query->whereNull('admin_reply');
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('room', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })->orWhere('comment', 'like', "%{$search}%");
            });
        }

        $feedbacks = $query->latest()->paginate(15);

        // Get rating statistics
        $rating5 = Feedback::where('rating', 5)->count();
        $rating4 = Feedback::where('rating', 4)->count();
        $rating3 = Feedback::where('rating', 3)->count();
        $rating2 = Feedback::where('rating', 2)->count();
        $rating1 = Feedback::where('rating', 1)->count();

        $totalFeedbacks = Feedback::count();
        $avgRating = Feedback::avg('rating') ?? 0;
        $unrepliedCount = Feedback::whereNull('admin_reply')->count();

        return view('admin.feedbacks', compact(
            'feedbacks',
            'rating5',
            'rating4',
            'rating3',
            'rating2',
            'rating1',
            'totalFeedbacks',
            'avgRating',
            'unrepliedCount'
        ));
    }

public function show(Feedback $feedback)
{
    // Load relationships including the user
    $feedback->load(['user', 'room', 'booking']);

    return response()->json([
        'success' => true,
        'data' => $feedback
    ]);
}

public function update(Request $request, Feedback $feedback)
{
    $request->validate([
        'admin_reply' => 'required|string|max:1000',
    ]);

    $feedback->update([
        'admin_reply' => $request->admin_reply,
        // Optional: updating status if you have the column
        'status' => 'replied' 
    ]);

    // Check if the request is AJAX/Fetch
    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => 'Reply sent successfully!'
        ]);
    }

    return redirect()->back()->with('success', 'Reply submitted successfully.');
}

    public function destroy(Feedback $feedback)
    {
        try {
            $feedback->delete();

            // ✅ Return JSON for fetch()
            return response()->json([
                'success' => true,
                'message' => 'Feedback deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete feedback.'
            ], 500);
        }
    }

    public function stats()
    {
        // Overall statistics
        $totalFeedbacks = Feedback::count();
        $avgRating = Feedback::avg('rating') ?? 0;
        $repliedCount = Feedback::whereNotNull('admin_reply')->count();

        // Rating distribution
        $ratingDistribution = Feedback::select('rating', DB::raw('count(*) as count'))
            ->groupBy('rating')
            ->orderBy('rating', 'desc')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->rating => $item->count];
            });

        // Monthly trends
        $monthlyTrends = Feedback::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as count'),
            DB::raw('avg(rating) as avg_rating')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top rooms by rating
        $topRooms = Room::withCount(['feedbacks as feedback_count'])
            ->withAvg('feedbacks', 'rating')
            ->having('feedback_count', '>', 0)
            ->orderByDesc('feedbacks_avg_rating')
            ->take(10)
            ->get();

        // Top users by feedback
        $topUsers = User::withCount(['feedbacks as feedback_count'])
            ->withAvg('feedbacks', 'rating')
            ->having('feedback_count', '>', 0)
            ->orderByDesc('feedback_count')
            ->take(10)
            ->get();

        return view('admin.feedbacks.stats', compact(
            'totalFeedbacks',
            'avgRating',
            'repliedCount',
            'ratingDistribution',
            'monthlyTrends',
            'topRooms',
            'topUsers'
        ));
    }

    public function export(Request $request)
    {
        $query = Feedback::with(['user', 'room']);

        if ($request->has('rating') && $request->rating != '') {
            $query->where('rating', $request->rating);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $feedbacks = $query->latest()->get();

        // You can implement CSV/Excel export here
        // For now, just return view
        return view('admin.feedbacks.export', compact('feedbacks'));
    }
}
