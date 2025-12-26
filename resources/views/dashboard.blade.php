@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('subtitle', 'Library Room Booking System')

@section('breadcrumb')
    <li class="text-gray-500">Dashboard</li>
@endsection

@section('content')
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-calendar-alt text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalBookings ?? 0 }}</h3>
                    <p class="text-gray-600">Total Bookings</p>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-green-600">
                    <i class="fas fa-arrow-up"></i> 12% from last month
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-door-open text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $availableRooms ?? 0 }}</h3>
                    <p class="text-gray-600">Available Rooms</p>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-600">
                    {{ $totalRooms ?? 0 }} total rooms
                </span>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $pendingBookings ?? 0 }}</h3>
                    <p class="text-gray-600">Pending Approval</p>
                </div>
            </div>
            <div class="mt-4">
                <a href="bookings?status=pending" class="text-sm text-indigo-600 hover:underline">
                    View all →
                </a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalUsers ?? 0 }}</h3>
                    <p class="text-gray-600">Total Users</p>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-sm text-gray-600">
                    {{ $activeUsers ?? 0 }} active today
                </span>
            </div>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Recent Bookings -->
        <div class="bg-white rounded-xl shadow-sm border">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Recent Bookings</h3>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-sm text-gray-600 border-b">
                                <th class="pb-3">Room</th>
                                <th class="pb-3">User</th>
                                <th class="pb-3">Date</th>
                                <th class="pb-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentBookings as $booking)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="py-4">
                                    <div class="font-medium">{{ $booking->room->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $booking->room->location ?? '' }}</div>
                                </td>
                                <td class="py-4">
                                    <div class="font-medium">{{ $booking->user->name ?? 'N/A' }}</div>
                                </td>
                                <td class="py-4">
                                    <div class="font-medium">{{ $booking->date->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($booking->start_time)->format('h:i A') }} -
                                    {{ \Carbon\Carbon::parse($booking->end_time)->format('h:i A') }}
                                    </div>
                                </td>
                                <td class="py-4">
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
                                    {{ ucfirst($booking->status) }}
                                </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-6 text-center">
                    <a href="{{ route('admin.bookings') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        View All Bookings →
                    </a>
                </div>
            </div>
        </div>

        <!-- Room Utilization -->
        <div class="bg-white rounded-xl shadow-sm border">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Room Utilization</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @foreach($roomUtilization as $room)
                    <div>
                        <div class="flex justify-between mb-1">
                            <span class="text-sm font-medium text-gray-700">{{ $room->name }}</span>
                            <span class="text-sm text-gray-500">{{ $room->utilization }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $room->utilization }}%"></div>
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            {{ $room->bookings_count }} bookings this week
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="#" 
               class="flex items-center p-4 border rounded-lg hover:bg-gray-50 hover:border-indigo-300">
                <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-plus text-indigo-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800">New Booking</h4>
                    <p class="text-sm text-gray-600">Create booking for user</p>
                </div>
            </a>
            <a href="{{ route('admin.rooms.create') }}" 
               class="flex items-center p-4 border rounded-lg hover:bg-gray-50 hover:border-indigo-300">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-door-open text-green-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800">Add Room</h4>
                    <p class="text-sm text-gray-600">Register new room</p>
                </div>
            </a>
            <a href="#" 
               class="flex items-center p-4 border rounded-lg hover:bg-gray-50 hover:border-indigo-300">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-comment-alt text-purple-600"></i>
                </div>
                <div>
                    <h4 class="font-medium text-gray-800">View Feedback</h4>
                    <p class="text-sm text-gray-600">Check user reviews</p>
                </div>
            </a>
        </div>
    </div>
@endsection

@section('scripts')
<script>

</script>
@endsection