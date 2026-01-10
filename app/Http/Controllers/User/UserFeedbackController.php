<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserFeedbackController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        $feedbacks = Feedback::with(['room', 'booking'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Calculate statistics
        $averageRating = Feedback::where('user_id', $user->id)
            ->avg('rating') ?? 0;
        
        $repliedCount = Feedback::where('user_id', $user->id)
            ->whereNotNull('admin_reply')
            ->count();
        
        return view('user.feedback', compact('feedbacks', 'averageRating', 'repliedCount'));
    }
    
    public function show($id)
    {
        $user = Auth::user();
        
        $feedback = Feedback::with(['room', 'booking'])
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        return response()->json([
            'success' => true,
            'feedback' => $feedback
        ]);
    }
    
  
    
    public function destroy($id)
    {
        $user = Auth::user();
        
        $feedback = Feedback::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();
        
        // Only allow deletion if no admin reply yet
        if ($feedback->admin_reply) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete feedback that has been replied to by admin.'
            ], 422);
        }
        
        $feedback->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Feedback deleted successfully.'
        ]);
    }
}