@extends('layouts.user')

@section('title', 'Guest Experiences')
@section('subtitle', 'Real stories from real guests who stayed with us')

@section('breadcrumb')
    <li><a href="{{ route('user.dashboard') }}" class="text-indigo-600 hover:text-indigo-800">Dashboard</a></li>
    <li class="text-gray-500">Customer Feedback</li>
@endsection

@section('content')
<style>
    :root {
        --accent: #6366f1;
        --accent-soft: #eef2ff;
        --dark: #0f172a;
        --text-main: #334155;
        --text-muted: #64748b;
        --bg-light: #f8fafc;
        --star-color: #f59e0b;
    }

    .stats-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        border-radius: 20px;
        transition: transform 0.3s ease;
    }

    .feedback-card {
        background: white;
        border-radius: 24px;
        border: 1px solid #f1f5f9;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    
    .feedback-card:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05);
        border-color: var(--accent-soft);
    }
    
    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        background: var(--accent-soft);
        color: var(--accent);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    .admin-reply {
        background: #f8fafc;
        border-radius: 18px;
        padding: 1.25rem;
        margin-top: 1.5rem;
        position: relative;
    }

    .admin-reply::before {
        content: '';
        position: absolute;
        top: -10px;
        left: 25px;
        border-left: 10px solid transparent;
        border-right: 10px solid transparent;
        border-bottom: 10px solid #f8fafc;
    }
</style>

<div class="max-w-6xl mx-auto space-y-8">
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="stats-card p-6 shadow-sm text-center">
            <div class="text-3xl font-extrabold text-slate-900">{{ $totalFeedbacks }}</div>
            <div class="text-sm font-medium text-slate-500 uppercase tracking-wider">Total Reviews</div>
        </div>
        <div class="stats-card p-6 shadow-sm text-center">
            <div class="text-3xl font-extrabold text-amber-500">
                {{ number_format($averageRating, 1) }} <span class="text-sm text-slate-300">/ 5</span>
            </div>
            <div class="text-sm font-medium text-slate-500 uppercase tracking-wider">Avg. Rating</div>
        </div>
        <div class="stats-card p-6 shadow-sm text-center border-l-4 border-indigo-500">
            <div class="text-3xl font-extrabold text-indigo-600">{{ $feedbackWithReply }}</div>
            <div class="text-sm font-medium text-slate-500 uppercase tracking-wider">Management Replies</div>
        </div>
    </div>

    <div class="space-y-6">
        @forelse($feedbacks as $feedback)
            <div class="feedback-card p-8 shadow-sm opacity-0 transform translate-y-4 transition-all duration-700 feedback-item">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                    <div class="flex items-center gap-4">
                        <div class="user-avatar">
                            {{ substr($feedback->user->name ?? 'G', 0, 1) }}
                        </div>
                        <div>
                            <h5 class="text-lg font-bold text-slate-800">{{ $feedback->user->name ?? 'Guest User' }}</h5>
                            <div class="flex items-center text-xs text-slate-400 gap-2">
                                <i class="far fa-calendar"></i>
                                {{ $feedback->created_at->format('M d, Y') }}
                            </div>
                        </div>
                    </div>
                    <div class="inline-flex items-center px-3 py-1 bg-amber-50 border border-amber-100 rounded-lg  font-bold text-md">
                        <i class="fas fa-star mr-1.5"></i> {{ number_format($feedback->rating, 1) }}
                    </div>
                </div>

                @if($feedback->room)
                    <div class="inline-flex items-center px-3 py-1.5 bg-slate-50 text-slate-600 rounded-md text-md font-semibold mb-4">
                        <i class="fas fa-door-open mr-2 text-indigo-500"></i>
                        {{ $feedback->room->name }}
                    </div>
                @endif

                <div class="text-slate-700 leading-relaxed italic text-lg mb-4">
                    <i class="fas fa-quote-left text-indigo-100 text-2xl mr-2"></i>
                    {{ $feedback->comment }}
                </div>

                @if($feedback->admin_reply)
                    <div class="admin-reply">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="flex items-center justify-center w-5 h-5 bg-indigo-600 rounded-full">
                                <i class="fas fa-check text-[20px] text-white"></i>
                            </span>
                            <span class="text-md font-bold text-indigo-900 uppercase tracking-tighter">Management Response</span>
                        </div>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            {{ $feedback->admin_reply }}
                        </p>
                    </div>
                @endif
            </div>
        @empty
            <div class="text-center py-20 bg-white rounded-3xl border border-dashed border-slate-300">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="far fa-comments text-3xl text-slate-300"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-700">No feedback yet</h3>
                <p class="text-slate-400">Be the first to share your experience!</p>
            </div>
        @endforelse
    </div>

    @if(method_exists($feedbacks, 'links'))
        <div class="mt-10">
            {{ $feedbacks->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const items = document.querySelectorAll('.feedback-item');
        
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.remove('opacity-0', 'translate-y-4');
                    entry.target.classList.add('opacity-100', 'translate-y-0');
                }
            });
        }, { threshold: 0.1 });

        items.forEach(item => observer.observe(item));
    });
</script>
@endsection