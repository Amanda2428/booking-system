@extends('layouts.admin')

@section('title', 'Bookings Management')
@section('subtitle', 'Manage and approve room bookings')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="text-gray-500">Bookings</li>
@endsection

@section('content')


    <div class="mb-6 bg-white rounded-xl shadow-sm border p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.bookings') }}"
                    class="px-4 py-2 rounded-lg {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All Booking
                </a>
                @foreach (['pending', 'approved', 'rejected', 'cancelled'] as $status)
                    <a href="{{ route('admin.bookings', ['status' => $status]) }}"
                        class="px-4 py-2 rounded-lg {{ request('status') == $status ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                        {{ ucfirst($status) }} ({{ ${$status . 'Count'} ?? 0 }})
                    </a>
                @endforeach
            </div>
            <div class="flex items-center gap-4">
                <input type="date" value="{{ request('date') }}"
                    onchange="window.location.href = '{{ route('admin.bookings') }}?date=' + this.value"
                    class="border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    <i class="fas fa-filter mr-2"></i> Filter
                </button>
            </div>
        </div>
    </div>


    <!-- Bookings Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Bookings List</h3>
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-600">{{ $bookings->total() }} total bookings</span>
                {{-- <a href="{{ route('admin.bookings.export') }}"
                    class="px-4 py-2 bg-green-600 text-black rounded-lg hover:bg-green-700 flex items-center">
                    <i class="fas fa-file-export mr-2"></i> Export
                </a> --}}
                <a href="{{ route('admin.bookings.create') }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                    <i class="fas fa-plus mr-2"></i> Add Booking
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-sm text-gray-600">
                        <th class="py-3 px-6 font-medium">Booking ID</th>
                        <th class="py-3 px-6 font-medium">Room Details</th>
                        <th class="py-3 px-6 font-medium">User</th>
                        <th class="py-3 px-6 font-medium">Date & Time</th>
                        <th class="py-3 px-6 font-medium">Status</th>
                        <th class="py-3 px-6 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-mono font-semibold text-gray-800">#{{ $booking->id }}</div>
                                <div class="text-xs text-gray-500 mt-1">{{ $booking->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium text-gray-800">{{ $booking->room->name }}</div>
                                <div class="text-sm text-gray-600">{{ $booking->room->location }}</div>
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-users mr-1"></i> {{ $booking->room->capacity }} capacity
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                        @if ($booking->user->profile)
                                            <img src="{{ asset('storage/' . $booking->user->profile) }}"
                                                alt="{{ $booking->user->name }}"
                                                class="w-10 h-10 rounded-full object-cover">
                                        @else
                                            <i class="fas fa-user text-indigo-600 text-m"></i>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="font-medium text-gray-800">{{ $booking->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium text-gray-800">{{ $booking->date->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                </div>
                                @php
                                    $duration = \Carbon\Carbon::parse($booking->end_time)->diffInHours(
                                        \Carbon\Carbon::parse($booking->start_time),
                                    );
                                @endphp
                                <div class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-clock mr-1"></i> {{ $duration }} hours
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'approved' => 'bg-green-100 text-green-800 border-green-200',
                                        'rejected' => 'bg-red-100 text-red-800 border-red-200',
                                        'cancelled' => 'bg-gray-100 text-gray-800 border-gray-200',
                                    ];
                                @endphp
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium border {{ $statusColors[$booking->status] }}">
                                    <i class="fas fa-circle mr-1 text-xs"></i>
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-1">
                                    <!-- View Button -->
                                    <button onclick="viewBooking({{ $booking->id }})"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    <!-- Quick Actions Dropdown -->
                                    @if ($booking->status == 'pending')
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false"
                                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border z-10">
                                                <a href="#" onclick="updateStatus({{ $booking->id }}, 'approved')"
                                                    class="flex items-center px-4 py-2 text-green-700 hover:bg-green-50">
                                                    <i class="fas fa-check mr-3"></i> Approve
                                                </a>
                                                <a href="#" onclick="updateStatus({{ $booking->id }}, 'rejected')"
                                                    class="flex items-center px-4 py-2 text-red-700 hover:bg-red-50">
                                                    <i class="fas fa-times mr-3"></i> Reject
                                                </a>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- <!-- Edit Button -->
                                <button onclick="editBooking({{ $booking->id }})"
                                        class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                        title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button> --}}

                                    <!-- Delete Button -->
                                    <button onclick="confirmDelete('booking', {{ $booking->id }})"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                        title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 font-medium">No bookings found</p>
                                    <p class="text-gray-400 text-sm mt-1">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($bookings->hasPages())
            <div class="p-6 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }}
                        results
                    </div>
                    <div class="flex items-center space-x-2">
                        {{ $bookings->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
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
                <div class="p-6 border-t flex justify-end space-x-3">
                    <button onclick="closeModal()"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        Close
                    </button>

                    {{-- <button onclick="editBooking(modalBookingId)" 
                            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-edit mr-2"></i> Edit Booking
                    </button> --}}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        let modalBookingId = null;

        function viewBooking(id) {
            // Added /admin prefix to match web.php
            fetch(`/admin/bookings/${id}`)
                .then(response => {
                    if (!response.ok) throw new Error('Booking not found');
                    return response.json();
                })
                .then(data => {
                    const booking = data.data;
                    document.getElementById('bookingDetails').innerHTML = `
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">ROOM INFORMATION</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-door-open text-gray-400 w-5"></i>
                                        <span class="ml-3 text-gray-800">${booking.room.name}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt text-gray-400 w-5"></i>
                                        <span class="ml-3 text-gray-800">${booking.room.location}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-users text-gray-400 w-5"></i>
                                        <span class="ml-3 text-gray-800">Capacity: ${booking.room.capacity} people</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">BOOKING TIMES</h4>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar text-gray-400 w-5"></i>
                                        <span class="ml-3 text-gray-800">${new Date(booking.date).toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-clock text-gray-400 w-5"></i>
                                        <span class="ml-3 text-gray-800">${booking.start_time} - ${booking.end_time}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">USER INFORMATION</h4>
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-user text-indigo-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-800">${booking.user.name}</div>
                                        <div class="text-sm text-gray-600">${booking.user.email}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">BOOKING STATUS</h4>
                                <div class="flex items-center">
                                    <span class="px-4 py-2 rounded-full text-sm font-medium ${getStatusColorClass(booking.status)}">
                                        <i class="fas fa-circle mr-2 text-xs"></i>
                                        ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                                    </span>
                                </div>
                            </div>
                            
                            ${booking.feedback ? `
                                            <div>
                                                <h4 class="text-sm font-medium text-gray-500 mb-2">FEEDBACK</h4>
                                                <div class="p-3 bg-yellow-50 border border-yellow-100 rounded-lg">
                                                    <div class="flex items-center mb-2">
                                                        <div class="text-yellow-500">
                                                            ${getStars(booking.feedback.rating)}
                                                        </div>
                                                        <span class="ml-2 text-sm font-medium">${booking.feedback.rating}/5</span>
                                                    </div>
                                                    <p class="text-gray-700">${booking.feedback.comment || 'No comment provided'}</p>
                                                    ${booking.feedback.admin_reply ? `
                                    <div class="mt-3 p-2 bg-white rounded border-l-4 border-blue-500">
                                        <p class="text-sm text-gray-600"><strong>Admin Reply:</strong> ${booking.feedback.admin_reply}</p>
                                    </div>
                                    ` : ''}
                                                </div>
                                            </div>
                                            ` : ''}
                        </div>
                    </div>
                `;
                    document.getElementById('viewModal').classList.remove('hidden');
                })
                .catch(err => alert(err.message));
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

        function getStars(rating) {
            let stars = '';
            for (let i = 1; i <= 5; i++) {
                stars += i <= rating ? '<i class="fas fa-star"></i>' : '<i class="far fa-star"></i>';
            }
            return stars;
        }

        function updateStatus(id, status) {
            if (confirm(`Are you sure you want to ${status} this booking?`)) {
                let url = "{{ route('admin.bookings.updateStatus', ':id') }}".replace(':id', id);

                fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            status: status
                        })
                    })
                    .then(res => {
                        // Check if response is okay before parsing JSON
                        if (!res.ok) {
                            return res.text().then(text => {
                                throw new Error(text)
                            });
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Update failed');
                        }
                    })
                    .catch(err => {
                        console.error('Error details:', err);
                        alert('You cannot approved for this because the booking is taken by other.');
                    });
            }
        }

        function editBooking(id) {
            window.location.href = `/admin/bookings/${id}`;
        }

        function confirmDelete(type, id) {
            if (confirm(`Are you sure you want to delete this ${type}? This action cannot be undone.`)) {
                fetch(`/admin/bookings/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Failed to delete');
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

        function searchBookings(query) {
            const url = new URL(window.location.href);
            if (query) {
                url.searchParams.set('search', query);
            } else {
                url.searchParams.delete('search');
            }
            window.location.href = url.toString();
        }

        function resetFilters() {
            window.location.href = '{{ route('admin.bookings') }}';
        }

        function exportBookings() {
            // Implement export functionality
            alert('Export feature coming soon!');
        }

        function closeModal() {
            document.getElementById('viewModal').classList.add('hidden');
        }

        // Close modal on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeModal();
        });
    </script>
@endsection
