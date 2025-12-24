@extends('layouts.admin')

@section('title', 'Bookings Management')
@section('subtitle', 'Manage room bookings and approvals')

@section('breadcrumb')
    <li class="text-gray-500">Bookings</li>
@endsection

@section('content')
    <!-- Filters -->
    <div class="mb-6 bg-white rounded-xl shadow-sm border p-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('admin.bookings') }}"
                    class="px-4 py-2 rounded-lg {{ !request('status') ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    All
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
    <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
        <div class="p-6 border-b flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-800">Bookings List</h3>
            <form method="GET" action="{{ route('admin.bookings.search') }}" class="relative">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search bookings..."
                    class="pl-10 pr-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-sm text-gray-600">
                        <th class="py-3 px-6">ID</th>
                        <th class="py-3 px-6">Room</th>
                        <th class="py-3 px-6">User</th>
                        <th class="py-3 px-6">Date & Time</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="py-4 px-6">
                                <div class="font-medium">#{{ $booking->id }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->created_at->format('M d, Y') }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium">{{ $booking->room->name }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->room->location }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium">{{ $booking->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $booking->user->email }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-medium">{{ $booking->date->format('M d, Y') }}</div>
                                <div class="text-sm text-gray-500"> {{ $booking->start_time->format('h:i A') }} -
                                    {{ $booking->end_time->format('h:i A') }}</div>
                            </td>
                            <td class="py-4 px-6">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'rejected' => 'bg-red-100 text-red-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                    ];
                                @endphp
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium {{ $statusColors[$booking->status] }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-2">
                                    <button onclick="viewBooking({{ $booking->id }})"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>

                                    @if ($booking->status == 'pending')
                                        <button onclick="updateStatusAndClose({{ $booking->id }}, 'approved')"
                                            class="p-2 text-green-600 hover:bg-green-50 rounded-lg" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        <button onclick="updateStatusAndClose({{ $booking->id }}, 'rejected')"
                                            class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Reject">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif

                                    <button onclick="confirmDelete({{ $booking->id }})"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-gray-500">
                                <i class="fas fa-calendar-times text-4xl mb-2"></i>
                                <p>No bookings found</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($bookings->hasPages())
            <div class="p-6 border-t">
                {{ $bookings->links() }}
            </div>
        @endif
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-xl w-full max-w-2xl mx-4">
                <div class="p-6 border-b flex justify-between items-center">
                    <h3 class="text-xl font-semibold">Booking Details</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times"></i>
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
    // VIEW BOOKING
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
                        <div>
                            <h4 class="font-bold text-gray-700 border-b mb-2">Room Info</h4>
                            <p><span class="font-medium mb-3">Name:</span> ${booking.room.name}</p>
                            <p><span class="font-medium">Location:</span> ${booking.room.location}</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-700 border-b mb-2">User Info</h4>
                            <p><span class="font-medium">Name:</span> ${booking.user.name}</p>
                            <p><span class="font-medium">Email:</span> ${booking.user.email}</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-700 border-b mb-2">Booking Details</h4>
                            <p><span class="font-medium">Date:</span> ${new Date(booking.date).toLocaleDateString()}</p>
                            <p><span class="font-medium">Time:</span> ${formatTime(booking.start_time)} - ${formatTime(booking.end_time)}</p>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-700 border-b mb-2">Status</h4>
                            <span class="px-3 py-1 rounded-full text-xs font-medium ${getStatusColor(booking.status)}">
                                ${booking.status.toUpperCase()}
                            </span>
                        </div>
                    </div>
                `;
                document.getElementById('viewModal').classList.remove('hidden');
            })
            .catch(err => alert(err.message));
    }

    // UPDATE STATUS (Approve/Reject)
    function updateStatusAndClose(id, status) {
        if (confirm(`Are you sure you want to ${status} this booking?`)) {
            fetch(`/admin/bookings/${id}`, { // Added /admin prefix
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    location.reload(); // Refresh to show updated status
                } else {
                    alert(data.message || 'Update failed');
                }
            })
            .catch(err => console.error(err));
        }
    }

    // DELETE
    function confirmDelete(id) {
        if (confirm('Are you sure you want to delete this booking?')) {
            fetch(`/admin/bookings/${id}`, { // Added /admin prefix
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) location.reload();
                else alert(data.message || 'Delete failed');
            });
        }
    }

    function closeModal() {
        document.getElementById('viewModal').classList.add('hidden');
    }

    function getStatusColor(status) {
        const colors = {
            'pending': 'bg-yellow-100 text-yellow-800',
            'approved': 'bg-green-100 text-green-800',
            'rejected': 'bg-red-100 text-red-800',
            'cancelled': 'bg-gray-100 text-gray-800'
        };
        return colors[status] || 'bg-gray-100';
    }

    function formatTime(timeStr) {
        if (!timeStr) return '';
        const [hour, min] = timeStr.split(':');
        const hourNum = parseInt(hour);
        const ampm = hourNum >= 12 ? 'PM' : 'AM';
        const hour12 = hourNum % 12 || 12;
        return `${hour12}:${min} ${ampm}`;
    }
</script>
@endsection
