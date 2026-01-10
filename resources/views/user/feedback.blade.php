@extends('layouts.user')

@section('title', 'My Feedback')
@section('subtitle', 'View and manage your room feedback')

@section('breadcrumb')
    <li><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('user.bookings') }}">My Bookings</a></li>
    <li class="text-gray-500">My Feedback</li>
@endsection

@section('content')
    <div class="space-y-6">

        <!-- Feedback List -->
        @if ($feedbacks->count() > 0)
            <div class="bg-white rounded-lg border overflow-hidden">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-semibold text-gray-800">All Feedback</h3>
                    <p class="text-gray-600 text-sm mt-1">View all feedback you've submitted</p>
                </div>

                <div class="divide-y">
                    @foreach ($feedbacks as $feedback)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                <!-- Room & Booking Info -->
                                <div class="flex-1">
                                    <div class="flex items-start mb-4">
                                        <div
                                            class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-door-open text-indigo-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-lg">{{ $feedback->room->name }}</h4>
                                            <p class="text-gray-600 text-sm">{{ $feedback->room->location }}</p>
                                            <p class="text-gray-500 text-sm mt-1">
                                                Booking #{{ $feedback->booking_id }} •
                                                {{ \Carbon\Carbon::parse($feedback->booking->date)->format('M d, Y') }}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- User Feedback -->
                                    <div class="ml-15">
                                        <div class="flex items-center mb-2">
                                            <div class="text-yellow-500 mr-3">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i
                                                        class="fas fa-star{{ $i > $feedback->rating ? '-regular' : '' }}"></i>
                                                @endfor
                                            </div>
                                            <span class="font-medium">{{ $feedback->rating }}/5</span>
                                        </div>

                                        @if ($feedback->comment)
                                            <div class="mt-3">
                                                <p class="text-gray-700">{{ $feedback->comment }}</p>
                                            </div>
                                        @endif

                                        <!-- Admin Reply -->
                                        @if ($feedback->admin_reply)
                                            <div class="mt-4 p-4 bg-blue-50 border-l-4 border-blue-500 rounded-r-lg">
                                                <div class="flex items-center mb-2">
                                                    <div
                                                        class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                                        <i class="fas fa-user-shield text-blue-600 text-sm"></i>
                                                    </div>
                                                    <span class="font-medium text-blue-700">Admin Reply</span>
                                                    <span class="text-gray-500 text-sm ml-auto">
                                                        {{ \Carbon\Carbon::parse($feedback->updated_at)->format('M d, Y') }}
                                                    </span>
                                                </div>
                                                <p class="text-gray-700">{{ $feedback->admin_reply }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status & Actions -->
                                <div class="md:text-right">
                                    <span
                                        class="inline-flex items-center px-3 py-2 rounded-full text-sm font-medium
                                        {{ $feedback->admin_reply ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        <i class="fas fa-{{ $feedback->admin_reply ? 'check-circle' : 'clock' }} mr-2"></i>
                                        {{ $feedback->admin_reply ? 'Replied' : 'Pending Reply' }}
                                    </span>

                                    <div class="mt-4 flex md:justify-end space-x-2">
                                        <button onclick="deleteFeedback({{ $feedback->id }})"
                                            class="px-3 py-1.5 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 text-sm">
                                            <i class="fas fa-trash mr-1"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if ($feedbacks->hasPages())
                    <div class="p-6 border-t">
                        {{ $feedbacks->links() }}
                    </div>
                @endif
            </div>
        @else
            <div class="text-center py-16 bg-white rounded-lg border">
                <i class="fas fa-star text-5xl text-gray-300 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-700 mb-3">No feedback submitted yet</h3>
                <p class="text-gray-600 mb-8 max-w-md mx-auto">
                    You haven't submitted any feedback yet. Feedback helps us improve our rooms and services.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('user.bookings') }}"
                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                        <i class="fas fa-calendar-alt mr-2"></i> View Bookings
                    </a>
                </div>
            </div>
        @endif

        <!-- Edit Feedback Modal -->
        <div id="editFeedbackModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-xl w-full max-w-md">
                    <div class="p-6 border-b flex justify-between items-center">
                        <h3 class="text-xl font-semibold text-gray-800">Edit Feedback</h3>
                        <button onclick="closeEditModal()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <form id="editFeedbackForm" class="p-6">
                        @csrf
                        @method('PUT')
                        <input type="hidden" name="feedback_id" id="editFeedbackId">

                        <div class="space-y-6">
                            <!-- Rating -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-3">Rating</label>
                                <div class="flex items-center justify-center space-x-2" id="editRatingStars">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <button type="button" onclick="setEditRating({{ $i }})"
                                            class="text-3xl text-gray-300 hover:text-yellow-400 transition-colors"
                                            data-rating="{{ $i }}">
                                            <i class="far fa-star"></i>
                                        </button>
                                    @endfor
                                </div>
                                <input type="hidden" name="rating" id="editRatingValue" value="5">
                            </div>

                            <!-- Comment -->
                            <div>
                                <label for="editComment" class="block text-sm font-medium text-gray-700 mb-2">
                                    Comments
                                </label>
                                <textarea id="editComment" name="comment" rows="4"
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                            </div>

                            <!-- Submit -->
                            <div class="pt-6 border-t">
                                <button type="submit"
                                    class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center justify-center">
                                    <i class="fas fa-save mr-2"></i> Update Feedback
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let editRating = 5;

        function editFeedback(feedbackId) {
            fetch(`/user/feedback/${feedbackId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('editFeedbackId').value = data.feedback.id;
                        document.getElementById('editComment').value = data.feedback.comment || '';
                        setEditRating(data.feedback.rating);
                        document.getElementById('editFeedbackModal').classList.remove('hidden');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('Failed to load feedback details');
                });
        }

        function setEditRating(rating) {
            editRating = rating;
            document.getElementById('editRatingValue').value = rating;

            const stars = document.querySelectorAll('#editRatingStars button');
            stars.forEach((star, index) => {
                const icon = star.querySelector('i');
                if (index < rating) {
                    icon.className = 'fas fa-star';
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-500');
                } else {
                    icon.className = 'far fa-star';
                    star.classList.remove('text-yellow-500');
                    star.classList.add('text-gray-300');
                }
            });
        }

        function closeEditModal() {
            document.getElementById('editFeedbackModal').classList.add('hidden');
        }

        function deleteFeedback(feedbackId) {
            if (confirm('Are you sure you want to delete this feedback?')) {
                fetch(`/user/feedback/${feedbackId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to delete feedback');
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                        alert('An error occurred while deleting feedback');
                    });
            }
        }

        // Handle edit form submission
        document.getElementById('editFeedbackForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const feedbackId = document.getElementById('editFeedbackId').value;

            fetch(`/user/feedback/${feedbackId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Feedback updated successfully!');
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to update feedback');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('An error occurred while updating feedback');
                });
        });

        // Initialize edit rating
        setEditRating(5);

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeEditModal();
            }
        });
    </script>
@endsection
