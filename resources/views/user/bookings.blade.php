@extends('layouts.user')

@section('title', 'My Bookings')
@section('subtitle', 'Manage your room bookings')

@section('breadcrumb')
    <li><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
    <li class="text-gray-500">My Bookings</li>
@endsection

@section('header-actions')
    <a href="{{ route('user.bookings.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
        <i class="fas fa-plus mr-2"></i> New Booking
    </a>
@endsection

@section('content')
    <div class="mb-6 bg-gray-50 rounded-lg p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('user.bookings') }}"
                    class="px-4 py-2 rounded-lg {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                    All Bookings
                </a>
                @foreach (['pending', 'approved', 'rejected', 'cancelled'] as $status)
                    <a href="{{ route('user.bookings', ['status' => $status]) }}"
                        class="px-4 py-2 rounded-lg {{ request('status') == $status ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
                        {{ ucfirst($status) }}
                    </a>
                @endforeach
            </div>
            <div class="flex items-center gap-2">
                <div class="relative">
                    <input type="date" value="{{ request('date') }}" onchange="filterByDate(this.value)"
                        class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="relative">
                    <input type="text" placeholder="Search bookings..." value="{{ request('search') }}"
                        onkeyup="searchBookings(this.value)"
                        class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>

    @if ($bookings && $bookings->count() > 0)
        <div class="space-y-6">
            @foreach ($bookings as $booking)
                <div class="bg-white rounded-lg border hover:shadow-md transition-shadow overflow-hidden">
                    <div class="p-6">
                        <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-start">
                                    <div class="w-16 h-16 rounded-lg bg-indigo-100 flex items-center justify-center mr-4">
                                        <i class="fas fa-door-open text-indigo-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-800 text-lg">{{ $booking->room->name }}</h3>
                                        <p class="text-gray-600">{{ $booking->room->location }}</p>
                                        <div class="flex items-center mt-2">
                                            <span
                                                class="px-3 py-1 rounded-full text-xs font-medium 
                                            {{ $booking->status == 'approved'
                                                ? 'bg-green-100 text-green-800'
                                                : ($booking->status == 'pending'
                                                    ? 'bg-yellow-100 text-yellow-800'
                                                    : ($booking->status == 'rejected'
                                                        ? 'bg-red-100 text-red-800'
                                                        : 'bg-gray-100 text-gray-800')) }}">
                                                {{ ucfirst($booking->status) }}
                                            </span>
                                            <span class="mx-3 text-gray-300">•</span>
                                            <span class="text-sm text-gray-600">
                                                <i class="fas fa-users mr-1"></i> {{ $booking->room->capacity }} capacity
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="text-lg font-bold text-gray-800">{{ $booking->date->format('M d, Y') }}</div>
                                <div class="text-gray-600">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                </div>
                                <div class="text-sm text-gray-500 mt-1">
                                    {{ \Carbon\Carbon::parse($booking->end_time)->diffInHours(\Carbon\Carbon::parse($booking->start_time)) }}
                                    hours
                                </div>
                            </div>
                        </div>

                        @if ($booking->purpose)
                            <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                <p class="text-gray-700">{{ $booking->purpose }}</p>
                            </div>
                        @endif

                        <div class="mt-6 flex flex-wrap gap-3">
                            <button onclick="viewBooking({{ $booking->id }})"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center text-sm">
                                <i class="fas fa-eye mr-2"></i> View Details
                            </button>

                            @if ($booking->status == 'pending' && $booking->date->isFuture())
                                <button onclick="cancelBooking({{ $booking->id }})"
                                    class="px-4 py-2 border border-red-300 text-red-600 rounded-lg hover:bg-red-50 flex items-center text-sm">
                                    <i class="fas fa-times mr-2"></i> Cancel
                                </button>
                            @endif



                            @if (!$booking->feedback && $booking->status == 'approved')
                                <button onclick="viewFeedback({{ $booking->id }})"
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center text-sm">
                                    <i class="fas fa-star mr-2"></i> Leave Feedback
                                </button>
                            @endif

                            @if ($booking->feedback)
                                <span class="px-4 py-2 bg-yellow-50 text-yellow-700 rounded-lg flex items-center text-sm">
                                    <i class="fas fa-star mr-2"></i> Feedback Submitted
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="px-6 py-3 bg-gray-50 border-t text-sm text-gray-600">
                        <div class="flex justify-between items-center">
                            <div>
                                Booking ID: #{{ $booking->id }}
                                <span class="mx-2">•</span>
                                Created: {{ $booking->created_at->format('M d, Y') }}
                            </div>
                            @if ($booking->updated_at != $booking->created_at)
                                <div>
                                    Last updated: {{ $booking->updated_at->format('M d, Y') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            @if ($bookings->hasPages())
                <div class="mt-6">
                    {{ $bookings->links() }}
                </div>
            @endif
        </div>
    @else
        <div class="text-center py-16">
            <i class="fas fa-calendar-times text-5xl text-gray-300 mb-6"></i>
            <h3 class="text-2xl font-bold text-gray-700 mb-3">No bookings found</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                @if (request('status') || request('date') || request('search'))
                    Try adjusting your filters to see more results
                @else
                    You haven't made any bookings yet. Book your first study room to get started!
                @endif
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('user.bookings.create') }}"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                    <i class="fas fa-plus mr-2"></i> Book a Room
                </a>
                @if (request('status') || request('date') || request('search'))
                    <a href="{{ route('user.bookings') }}"
                        class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center">
                        <i class="fas fa-filter mr-2"></i> Clear Filters
                    </a>
                @endif
            </div>
        </div>
    @endif

    <div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl w-full max-w-2xl">
                <div class="p-6 border-b flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-800">Booking Details</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6" id="bookingDetails">
                </div>
            </div>
        </div>
    </div>

    <div id="feedbackModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl w-full max-w-md">
                <div class="p-6 border-b flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-800">Leave Feedback</h3>
                    <button onclick="closeFeedbackModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="feedbackForm" class="p-6">
                    @csrf
                    <input type="hidden" name="booking_id" id="feedbackBookingId">

                    <div class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">Rating</label>
                            <div class="flex items-center justify-center space-x-2" id="ratingStars">
                                @for ($i = 1; $i <= 5; $i++)
                                    <button type="button" onclick="setRating({{ $i }})"
                                        class="text-3xl text-gray-300 hover:text-yellow-400 transition-colors"
                                        data-rating="{{ $i }}">
                                        <i class="far fa-star"></i>
                                    </button>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="ratingValue" value="5">
                        </div>

                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                                Comments (Optional)
                            </label>
                            <textarea id="comment" name="comment" rows="4"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="Share your experience with this room..."></textarea>
                        </div>

                        <div class="pt-6 border-t">
                            <button type="submit"
                                class="w-full px-4 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center justify-center">
                                <i class="fas fa-paper-plane mr-2"></i> Submit Feedback
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let currentRating = 5;

        function viewBooking(id) {
            fetch(`/user/bookings/${id}/details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const booking = data.booking;

                        // Fix for Date comparison in JS
                        const bookingDateOnly = booking.date.split('T')[0];
                        const endDateTime = new Date(`${bookingDateOnly}T${booking.end_time}`);
                        const now = new Date();
                        const isPast = endDateTime < now;

                        document.getElementById('bookingDetails').innerHTML = `
                    <div class="space-y-6">
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                            <div class="w-16 h-16 rounded-lg bg-indigo-100 flex items-center justify-center mr-4">
                                <i class="fas fa-door-open text-indigo-600 text-2xl"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg">${booking.room.name}</h4>
                                <p class="text-gray-600">${booking.room.location}</p>
                                <div class="flex items-center mt-2">
                                    <i class="fas fa-users text-gray-400 mr-2"></i>
                                    <span class="text-sm text-gray-600">${booking.room.capacity} people capacity</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h5 class="text-sm font-medium text-gray-500 mb-2">BOOKING INFORMATION</h5>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm text-gray-500">Date</p>
                                        <p class="font-medium">${new Date(booking.date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Time</p>
                                        <p class="font-medium">${booking.start_time} - ${booking.end_time}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Duration</p>
                                        <p class="font-medium">${calculateDuration(booking.start_time, booking.end_time)}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h5 class="text-sm font-medium text-gray-500 mb-2">STATUS</h5>
                                <div class="space-y-3">
                                    <div>
                                        <p class="text-sm text-gray-500">Booking Status</p>
                                        <span class="px-3 py-1 rounded-full text-sm font-medium ${getStatusColor(booking.status)}">
                                            ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Booking ID</p>
                                        <p class="font-medium">#${booking.id}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Created</p>
                                        <p class="font-medium">${new Date(booking.created_at).toLocaleString()}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        ${booking.purpose ? `
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 mb-2">PURPOSE</h5>
                                    <div class="p-4 bg-gray-50 rounded-lg">
                                        <p class="text-gray-700">${booking.purpose}</p>
                                    </div>
                                </div>` : ''}
                        
                        ${booking.feedback ? `
                                <div>
                                    <h5 class="text-sm font-medium text-gray-500 mb-2">YOUR FEEDBACK</h5>
                                    <div class="p-4 bg-yellow-50 border border-yellow-100 rounded-lg">
                                        <div class="flex items-center mb-2">
                                            <div class="text-yellow-500">${getStars(booking.feedback.rating)}</div>
                                            <span class="ml-2 font-medium">${booking.feedback.rating}/5</span>
                                        </div>
                                        ${booking.feedback.comment ? `<p class="text-gray-700 mt-2">${booking.feedback.comment}</p>` : ''}
                                        ${booking.feedback.admin_reply ? `
                                        <div class="mt-4 p-3 bg-white rounded border-l-4 border-blue-500">
                                            <p class="text-sm text-gray-600"><strong>Admin Reply:</strong> ${booking.feedback.admin_reply}</p>
                                        </div>` : ''}
                                    </div>
                                </div>` : ''}
                        
                        <div class="pt-6 border-t flex justify-end space-x-3">
                            <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                                Close
                            </button>
                            ${booking.status === 'pending' && new Date(booking.date) >= new Date() ? `
                                    <button onclick="cancelBooking(${booking.id})" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                        Cancel Booking
                                    </button>` : ''}
                            ${!booking.feedback && booking.status === 'approved' && isPast ? `
                                    <button onclick="viewFeedback(${booking.id})" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                        Leave Feedback
                                    </button>` : ''}
                        </div>
                    </div>
                `;
                        document.getElementById('bookingModal').classList.remove('hidden');
                    }
                });
        }

        function viewFeedback(bookingId) {
            document.getElementById('feedbackBookingId').value = bookingId;
            resetRating();
            document.getElementById('feedbackModal').classList.remove('hidden');
            document.getElementById('bookingModal').classList.add('hidden');
        }

        function setRating(rating) {
            currentRating = rating;
            document.getElementById('ratingValue').value = rating;
            const stars = document.querySelectorAll('#ratingStars button');
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

        function resetRating() {
            setRating(5);
        }

        function getStars(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += i <= rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
            }
            return stars;
        }

        function calculateDuration(start, end) {
            const startTime = new Date(`2000-01-01T${start}`);
            const endTime = new Date(`2000-01-01T${end}`);
            const diff = (endTime - startTime) / (1000 * 60 * 60);
            return `${diff.toFixed(1)} hours`;
        }

        function getStatusColor(status) {
            const colors = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'approved': 'bg-green-100 text-green-800',
                'rejected': 'bg-red-100 text-red-800',
                'cancelled': 'bg-gray-100 text-gray-800'
            };
            return colors[status] || 'bg-gray-100 text-gray-800';
        }

        function cancelBooking(id) {
            if (confirm('Are you sure you want to cancel this booking?')) {
                fetch(`/user/bookings/${id}/cancel`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to cancel booking');
                        }
                    });
            }
        }

        function closeModal() {
            document.getElementById('bookingModal').classList.add('hidden');
        }

        function closeFeedbackModal() {
            document.getElementById('feedbackModal').classList.add('hidden');
        }

        function filterByDate(date) {
            const url = new URL(window.location.href);
            if (date) url.searchParams.set('date', date);
            else url.searchParams.delete('date');
            window.location.href = url.toString();
        }

        function searchBookings(query) {
            const url = new URL(window.location.href);
            if (query) url.searchParams.set('search', query);
            else url.searchParams.delete('search');
            // Using a debounce or waiting for enter would be better, but simple search:
            if (event.key === 'Enter') window.location.href = url.toString();
        }

        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const bookingId = document.getElementById('feedbackBookingId').value;

            fetch(`/user/bookings/${bookingId}/feedback`, {
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
                        alert('Feedback submitted successfully!');
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to submit feedback');
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('An error occurred while submitting feedback');
                });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeFeedbackModal();
            }
        });
    </script>
@endsection
