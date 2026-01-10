@extends('layouts.user')

@section('title', 'Dashboard')
@section('subtitle', 'Welcome back, ' . auth()->user()->name)

@section('breadcrumb')
    <li class="text-gray-500">Dashboard</li>
@endsection

@section('header-actions')
    <div class="flex space-x-3">
        <!-- View All Bookings Button -->
        <a href="{{ route('user.bookings') }}" 
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 flex items-center border border-gray-300">
            <i class="fas fa-list mr-2"></i> View Bookings
        </a>
        
        <!-- New Booking Button -->
        <a href="#" 
           class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
            <i class="fas fa-plus mr-2"></i> New Booking
        </a>
    </div>
@endsection

@section('content')
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-blue-600 font-medium">Active Bookings</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $activeBookings ?? 0 }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        <a href="{{ route('user.bookings', ['status' => 'approved']) }}" 
                           class="text-blue-600 hover:text-blue-800 hover:underline">
                            View all →
                        </a>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-green-600 font-medium">Pending Requests</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $pendingBookings ?? 0 }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        <a href="{{ route('user.bookings', ['status' => 'pending']) }}" 
                           class="text-green-600 hover:text-green-800 hover:underline">
                            View all →
                        </a>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-clock text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-gradient-to-r from-purple-50 to-violet-50 rounded-xl p-6 border border-purple-100">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-purple-600 font-medium">Total Bookings</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalBookings ?? 0 }}</h3>
                    <p class="text-sm text-gray-600 mt-1">
                        <a href="{{ route('user.bookings') }}" 
                           class="text-purple-600 hover:text-purple-800 hover:underline">
                            View history →
                        </a>
                    </p>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Bookings section with header -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-800">Your Bookings</h2>
            <a href="{{ route('user.bookings') }}" 
               class="text-indigo-600 hover:text-indigo-800 font-medium flex items-center">
                <span>View All Bookings</span>
                <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        
        @if($upcomingBookings && $upcomingBookings->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($upcomingBookings as $booking)
                <div class="bg-white rounded-lg border hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-bold text-gray-800">{{ $booking->room->name }}</h3>
                                <p class="text-sm text-gray-600">{{ $booking->room->location }}</p>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-medium 
                                {{ $booking->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                   ($booking->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($booking->status == 'rejected' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-calendar mr-3 w-5"></i>
                                <span>{{ \Carbon\Carbon::parse($booking->date)->format('D, M d, Y') }}</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-clock mr-3 w-5"></i>
                                <span>{{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} - 
                                      {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}</span>
                            </div>
                            @if($booking->purpose)
                            <div class="flex items-start text-gray-600">
                                <i class="fas fa-sticky-note mr-3 w-5 mt-1"></i>
                                <span class="text-sm">{{ Str::limit($booking->purpose, 60) }}</span>
                            </div>
                            @endif
                        </div>
                        
                        <div class="mt-6 flex justify-between">
                            <button onclick="viewBooking({{ $booking->id }})"
                                    class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                View Details
                            </button>
                            @if($booking->status == 'pending' && $booking->date >= now()->toDateString())
                            <button onclick="cancelBooking({{ $booking->id }})"
                                    class="text-red-600 hover:text-red-800 text-sm font-medium">
                                Cancel
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="bg-gray-50 border rounded-lg p-8 text-center">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-calendar text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">No upcoming bookings</h3>
                <p class="text-gray-600 mb-4">You don't have any bookings scheduled.</p>
                <a href="{{ route('user.bookings.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i> Make Your First Booking
                </a>
            </div>
        @endif
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Available Rooms -->
        <div>
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-xl font-bold text-gray-800">Available Rooms Now</h2>
                <a href="{{ route('user.rooms') }}" 
                   class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    Browse All →
                </a>
            </div>
            
            @if($availableRooms && $availableRooms->count() > 0)
                <div class="space-y-4">
                    @foreach($availableRooms->take(3) as $room)
                    <div class="flex items-center p-4 border rounded-lg hover:bg-gray-50">
                        <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center mr-4">
                            <i class="fas fa-door-open text-green-600"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-800">{{ $room->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $room->location }}</p>
                            <div class="flex items-center mt-1">
                                <i class="fas fa-users text-gray-400 text-xs mr-2"></i>
                                <span class="text-xs text-gray-500">{{ $room->capacity }} people</span>
                            </div>
                        </div>
                        <a href="{{ route('user.bookings.create', ['room_id' => $room->id]) }}"
                           class="px-3 py-1 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700">
                            Book Now
                        </a>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 border rounded-lg bg-gray-50">
                    <i class="fas fa-door-closed text-3xl text-gray-300 mb-3"></i>
                    <p class="text-gray-600">No rooms available at the moment</p>
                </div>
            @endif
        </div>
        
        <!-- Recent Activity -->
        <div>
            <h2 class="text-xl font-bold text-gray-800 mb-4">Recent Activity</h2>
            <div class="space-y-4">
                @if($recentActivity && $recentActivity->count() > 0)
                    @foreach($recentActivity as $activity)
                    <div class="flex items-start p-4 border rounded-lg">
                        <div class="w-10 h-10 rounded-full 
                            {{ $activity['type'] == 'booking' ? 'bg-blue-100' : 
                               ($activity['type'] == 'feedback' ? 'bg-yellow-100' : 'bg-gray-100') }} 
                            flex items-center justify-center mr-4">
                            <i class="fas 
                                {{ $activity['type'] == 'booking' ? 'fa-calendar-alt text-blue-600' : 
                                   ($activity['type'] == 'feedback' ? 'fa-star text-yellow-600' : 'fa-bell text-gray-600') }}"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-gray-800">{{ $activity['message'] }}</p>
                            <p class="text-sm text-gray-500 mt-1">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="text-center py-8 border rounded-lg bg-gray-50">
                        <i class="fas fa-history text-3xl text-gray-300 mb-3"></i>
                        <p class="text-gray-600">No recent activity</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions Bar -->
    <div class="mt-8 pt-8 border-t">
        <h2 class="text-lg font-bold text-gray-800 mb-4">Quick Actions</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('user.bookings') }}" 
               class="flex items-center px-5 py-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fas fa-list text-gray-600 mr-3"></i>
                <div>
                    <p class="font-medium text-gray-800">View All Bookings</p>
                    <p class="text-sm text-gray-600">Check your booking history</p>
                </div>
            </a>
            
            <a href="{{ route('user.bookings.create') }}" 
               class="flex items-center px-5 py-3 bg-indigo-50 border border-indigo-100 rounded-lg hover:bg-indigo-100">
                <i class="fas fa-plus-circle text-indigo-600 mr-3"></i>
                <div>
                    <p class="font-medium text-gray-800">New Booking</p>
                    <p class="text-sm text-gray-600">Book a room now</p>
                </div>
            </a>
            
            <a href="{{ route('user.rooms') }}" 
               class="flex items-center px-5 py-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
                <i class="fas fa-door-open text-gray-600 mr-3"></i>
                <div>
                    <p class="font-medium text-gray-800">Browse Rooms</p>
                    <p class="text-sm text-gray-600">View all available rooms</p>
                </div>
            </a>
        </div>
    </div>

    <!-- Booking Modal -->
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
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
function viewBooking(id) {
    fetch(`/user/bookings/${id}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const booking = data.booking;
                document.getElementById('bookingDetails').innerHTML = `
                    <div class="space-y-6">
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg">
                            <div class="w-12 h-12 rounded-lg bg-indigo-100 flex items-center justify-center mr-4">
                                <i class="fas fa-door-open text-indigo-600"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">${booking.room.name}</h4>
                                <p class="text-sm text-gray-600">${booking.room.location}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Date</p>
                                <p class="font-medium">${new Date(booking.date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Time</p>
                                <p class="font-medium">${formatTime(booking.start_time)} - ${formatTime(booking.end_time)}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Duration</p>
                                <p class="font-medium">${calculateDuration(booking.start_time, booking.end_time)}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <span class="px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(booking.status)}">
                                    ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                                </span>
                            </div>
                        </div>
                        
                        ${booking.purpose ? `
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Purpose</p>
                            <p class="text-gray-700 bg-gray-50 p-3 rounded">${booking.purpose}</p>
                        </div>
                        ` : ''}
                        
                        <div class="flex justify-end space-x-3 pt-6 border-t">
                            <a href="/user/bookings" 
                               class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center">
                                <i class="fas fa-list mr-2"></i>
                                View All Bookings
                            </a>
                            <button onclick="closeModal()" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                                Close
                            </button>
                            ${booking.status === 'pending' && new Date(booking.date) >= new Date() ? `
                            <button onclick="cancelBooking(${booking.id})" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Cancel Booking
                            </button>
                            ` : ''}
                        </div>
                    </div>
                `;
                document.getElementById('bookingModal').classList.remove('hidden');
            }
        });
}

function formatTime(timeString) {
    const [hours, minutes, seconds] = timeString.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const hour12 = hour % 12 || 12;
    return `${hour12}:${minutes} ${ampm}`;
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

function calculateDuration(start, end) {
    const startTime = new Date(`2000-01-01T${start}`);
    const endTime = new Date(`2000-01-01T${end}`);
    const diff = (endTime - startTime) / (1000 * 60 * 60);
    return `${diff} hours`;
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

function closeModal() {
    document.getElementById('bookingModal').classList.add('hidden');
}

document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') closeModal();
});
</script>
@endsection