@extends('layouts.admin')

@section('title', 'Feedbacks & Reviews')
@section('subtitle', 'Manage user feedback and ratings')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="text-gray-500">Feedbacks</li>
@endsection

@section('content')
    <!-- Stats -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-5 gap-4">
        @foreach ([5 => 'Excellent', 4 => 'Good', 3 => 'Average', 2 => 'Poor', 1 => 'Bad'] as $rating => $label)
            <div class="bg-white rounded-lg shadow-sm border p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">{{ $label }}</p>
                        <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ ${'rating' . $rating} ?? 0 }}</h3>
                    </div>
                    <div
                        class="w-12 h-12 rounded-full {{ $rating >= 4 ? 'bg-green-100' : ($rating >= 3 ? 'bg-yellow-100' : 'bg-red-100') }} flex items-center justify-center">
                        <div class="text-{{ $rating >= 4 ? 'green' : ($rating >= 3 ? 'yellow' : 'red') }}-600 font-bold">
                            {{ str_repeat('★', $rating) }}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>


    <!-- Filters -->
    <div class="mb-6 bg-white rounded-lg shadow-sm border p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.feedbacks') }}"
                    class="px-4 py-2 rounded-lg {{ !request('rating') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All Feedbacks
                </a>
                @foreach ([5, 4, 3, 2, 1] as $rating)
                    <a href="{{ route('admin.feedbacks', ['rating' => $rating]) }}"
                        class="px-4 py-2 rounded-lg {{ request('rating') == $rating ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ $rating }} ★
                    </a>
                @endforeach
                <a href="{{ route('admin.feedbacks', ['unreplied' => true]) }}"
                    class="px-4 py-2 rounded-lg {{ request('unreplied') ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    Unreplied
                </a>
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <input type="date" value="{{ request('date') }}" onchange="filterByDate(this.value)"
                        class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="relative">
                    <input type="text" placeholder="Search feedback..." value="{{ request('search') }}"
                        onkeyup="searchFeedbacks(this.value)"
                        class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Feedbacks List -->
    <div class="space-y-6">
        @forelse($feedbacks as $feedback)
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-user text-indigo-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">{{ $feedback->user->name }}</h4>
                                <p class="text-sm text-gray-600">{{ $feedback->user->email }}</p>
                                <div class="flex items-center mt-1">
                                    <div class="text-yellow-500">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $feedback->rating ? '' : '-o' }} text-sm"></i>
                                        @endfor
                                    </div>
                                    <span class="ml-2 text-sm text-gray-500">{{ $feedback->rating }}/5</span>
                                    <span class="mx-2 text-gray-300">•</span>
                                    <span
                                        class="text-sm text-gray-500">{{ $feedback->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="viewFeedback({{ $feedback->id }})"
                                class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="replyToFeedback({{ $feedback->id }})"
                                class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Reply">
                                <i class="fas fa-reply"></i>
                            </button>
                            <button onclick="confirmDelete('feedback', {{ $feedback->id }})"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="mb-4">
                        <div class="flex items-center mb-2">
                            <div class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center mr-3">
                                <i class="fas fa-door-open text-gray-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">{{ $feedback->room->name }}</p>
                                <p class="text-sm text-gray-600">{{ $feedback->room->location }}</p>
                            </div>
                        </div>

                        @if ($feedback->comment)
                            <div class="mt-3 p-4 bg-gray-50 rounded-lg">
                                <p class="text-gray-700">{{ $feedback->comment }}</p>
                            </div>
                        @endif

                        @if ($feedback->admin_reply)
                            <div class="mt-3 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                                <div class="flex items-center mb-2">
                                    <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center mr-2">
                                        <i class="fas fa-user-shield text-blue-600 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium text-blue-800">Admin Reply</span>
                                    <span
                                        class="ml-auto text-xs text-blue-600">{{ $feedback->updated_at->format('M d, Y') }}</span>
                                </div>
                                <p class="text-blue-700">{{ $feedback->admin_reply }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- Related Booking -->
                    <div class="pt-4 border-t">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-600">
                                <i class="fas fa-calendar-alt mr-1"></i>
                                Booking on {{ $feedback->booking->date->format('M d, Y') }}
                                from {{ \Carbon\Carbon::parse($feedback->booking->start_time)->format('h:i A') }}
                            </div>
                            <span
                                class="px-3 py-1 rounded-full text-xs font-medium 
                            {{ $feedback->booking->status == 'approved'
                                ? 'bg-green-100 text-green-800'
                                : ($feedback->booking->status == 'pending'
                                    ? 'bg-yellow-100 text-yellow-800'
                                    : ($feedback->booking->status == 'rejected'
                                        ? 'bg-red-100 text-red-800'
                                        : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($feedback->booking->status) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                <i class="fas fa-comments text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-medium text-gray-700 mb-2">No feedback found</h3>
                <p class="text-gray-500">No feedback has been submitted yet.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($feedbacks->hasPages())
        <div class="mt-6">
            {{ $feedbacks->links() }}
        </div>
    @endif


    <!-- Reply Modal -->
    <div id="replyModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
        <div class="bg-white rounded-lg w-full max-w-lg">
            <form id="replyForm" method="POST" class="space-y-4">
                @csrf
                <div class="p-6 space-y-4">
                    <input type="hidden" name="feedback_id" id="feedbackId">

                    <!-- Feedback Preview -->
                    <div id="feedbackPreview">
                        <!-- Loaded via JS -->
                    </div>

                    <!-- Admin Reply -->
                    <div>
                        <label for="admin_reply" class="block text-sm font-medium text-gray-700 mb-2">
                            Your Reply *
                        </label>
                        <textarea id="admin_reply" name="admin_reply" rows="4" required
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="Type your reply here..."></textarea>
                    </div>
                </div>

                <div class="p-6 border-t flex justify-end space-x-3">
                    <button type="button" onclick="closeReplyModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Send Reply
                    </button>
                </div>
            </form>
        </div>
    </div>


    <!-- View Modal -->
    <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl w-full max-w-2xl">
                <div class="p-6 border-b flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-800">Feedback Details</h3>
                    <button onclick="closeViewModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6" id="feedbackDetails">
                    <!-- Content loaded via AJAX -->
                </div>
                <div class="p-6 border-t flex justify-end">
                    <button onclick="closeViewModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function viewFeedback(id) {
            fetch(`/admin/feedbacks/${id}`)
                .then(response => response.json())
                .then(result => {
                    if (!result.success) throw new Error("Data error");

                    const feedback = result.data; // This is the fix

                    // Profile logic
                    const avatarHtml = feedback.user?.profile_image ?
                        `<img src="/storage/${feedback.user.profile_image}" class="w-full h-full object-cover">` :
                        `<i class="fas fa-user text-indigo-600"></i>`;

                    document.getElementById('feedbackDetails').innerHTML = `
                <div class="space-y-4">
                    <div class="flex items-center p-3 bg-gray-50 rounded-lg space-x-3">
                        <div class="w-12 h-12 rounded-full bg-indigo-100 flex items-center justify-center overflow-hidden border">
                            ${avatarHtml}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-800">${feedback.user?.name || 'Unknown'}</h4>
                            <p class="text-md text-gray-500">${feedback.user?.email || ''}</p>
                            <div class="mt-1">${getStars(feedback.rating)}</div>
                        </div>
                    </div>

                    <div>
                        <h4 class="text-md font-bold text-gray-400 tracking-wider mb-2">FEEDBACK</h4>
                        <div class="p-3 bg-white border rounded-lg italic text-gray-700">
                            "${feedback.comment || 'No comment provided'}"
                        </div>
                    </div>

                    <div>
                        <h4 class="text-md font-bold text-gray-400 tracking-wider mb-2">ROOM & BOOKING</h4>
                        <div class="p-3 border rounded-lg text-md space-y-2">
                            <p><strong>Room:</strong> ${feedback.room?.name || 'N/A'}</p>
                            <p><strong>Date:</strong> ${feedback.booking?.date || 'N/A'}</p>
                            <span class="px-2 py-1 rounded-full text-xs font-semibold ${getStatusColorClass(feedback.booking?.status || '')}">
                                ${feedback.booking?.status || 'Unknown'}
                            </span>
                        </div>
                    </div>
                </div>
                ${feedback.admin_reply ? `
                <div class="pt-2">
                    <h4 class="text-sm font-black text-blue-500 tracking-widest mb-2 uppercase">ADMIN RESPONSE</h4>
                    <div class="p-4 bg-blue-50 border border-blue-100 rounded-xl space-y-2">
                        <p class="text-blue-900 text-base leading-relaxed whitespace-pre-wrap">${feedback.admin_reply}</p>
                    </div>
                </div>
            ` : `
                <div class="text-center p-4 border-2 border-dashed border-gray-100 rounded-xl">
                    <p class="text-sm text-gray-400 font-medium italic">No admin reply yet.</p>
                </div>
            `}
            `;
                    document.getElementById('viewModal').classList.remove('hidden');
                })
                .catch(err => alert("Could not load feedback details."));
        }

        function replyToFeedback(id) {
            fetch(`/admin/feedbacks/${id}`)
                .then(response => response.json())
                .then(result => {
                    const feedback = result.data;
                    // IMPORTANT: Set the hidden ID field so the submit listener can find it
                    document.getElementById('feedbackId').value = feedback.id;
                    document.getElementById('admin_reply').value = feedback.admin_reply || '';
                    document.getElementById('replyModal').classList.remove('hidden');
                });
        }
        // Close modal
        function closeReplyModal() {
            document.getElementById('replyModal').classList.add('hidden');
        }
        document.getElementById('replyForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const id = document.getElementById('feedbackId').value;
            const formData = new FormData(this);

            fetch(`/admin/feedbacks/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json' // Tells Laravel to return JSON
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Server error');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Success! Reload the page to show the new reply
                        location.reload();
                    } else {
                        alert('Failed to send reply: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while sending the reply.');
                });
        });

        function getStars(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += i <= rating ?
                    '<i class="fas fa-star text-yellow-500"></i>' :
                    '<i class="far fa-star text-gray-300"></i>';
            }
            return stars;
        }


        function getStatusColorClass(status) {
            const colors = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800',
                'cancelled': 'bg-gray-100 text-gray-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }

        function confirmDelete(type, id) {
            if (confirm(`Are you sure you want to delete this ${type}? This action cannot be undone.`)) {
                fetch(`/admin/feedbacks/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success || data.message) {
                            location.reload();
                        } else {
                            alert('Failed to delete user');
                        }
                    });
            }
        }


        function filterByDate(date) {
            const url = new URL(window.location.href);
            if (date) {
                url.searchParams.set('date', date);
            } else {
                url.searchParams.delete('date');
            }
            window.location.href = url.toString();
        }

        function searchFeedbacks(query) {
            const url = new URL(window.location.href);
            if (query) {
                url.searchParams.set('search', query);
            } else {
                url.searchParams.delete('search');
            }
            window.location.href = url.toString();
        }

        function closeReplyModal() {
            document.getElementById('replyModal').classList.add('hidden');
        }

        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
        }




        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeReplyModal();
                closeViewModal();
            }
        });
    </script>
@endsection
