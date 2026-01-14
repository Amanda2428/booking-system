@extends('layouts.admin')

@section('title', 'Rooms Management')
@section('subtitle', 'Manage library rooms and availability')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="text-gray-500">Rooms</li>
@endsection

@section('content')
    <!-- Stats and Actions -->
    <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Total Rooms</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalRooms }}</h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <i class="fas fa-door-open text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Available Now</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $availableRooms }}</h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Average Capacity</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $avgCapacity }}</h3>
                </div>
                <div class="w-12 h-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <i class="fas fa-users text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Header with Actions -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Rooms List</h2>
            <p class="text-gray-600">Manage all library study rooms</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="relative">
                <input type="text" 
                       placeholder="Search rooms..." 
                       value="{{ request('search') }}"
                       onkeyup="searchRooms(this.value)"
                       class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full md:w-64">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            <a href="{{ route('admin.rooms.create') }}" 
               class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <i class="fas fa-plus mr-2"></i> Add Room
            </a>
        </div>
    </div>

    <!-- Rooms Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($rooms as $room)
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                <!-- Room Header -->
                <div class="p-6 border-b">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $room->name }}</h3>
                            <div class="flex items-center mt-1">
                                <i class="fas fa-map-marker-alt text-gray-400 text-sm mr-2"></i>
                                <span class="text-sm text-gray-600">{{ $room->location }}</span>
                            </div>
                        </div>
                        <span
                            class="px-3 py-1 rounded-full text-xs font-medium {{ $room->availability_status == 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($room->availability_status) }}
                        </span>
                    </div>
                </div>

                <!-- Room Details -->
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                                    <i class="fas fa-users text-blue-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-500">Capacity</p>
                                    <p class="font-medium">{{ $room->capacity }} people</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center">
                                    <i class="fas fa-tag text-purple-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-500">Category</p>
                                    <p class="font-medium">{{ $room->category->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        @if ($room->description)
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Description</p>
                                <p class="text-gray-700 line-clamp-2">{{ $room->description }}</p>
                            </div>
                        @endif

                        <!-- Stats -->
                        <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-800">{{ $room->bookings_count ?? 0 }}</p>
                                <p class="text-xs text-gray-500">Total Bookings</p>
                            </div>
                            <div class="text-center">
                                <p class="text-2xl font-bold text-gray-800">{{ $room->avg_rating ?? 0 }}/5</p>
                                <p class="text-xs text-gray-500">Avg Rating</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="p-6 border-t bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div class="flex space-x-2">
                            <button onclick="viewRoom({{ $room->id }})"
                                class="px-3 py-2 text-blue-600 hover:bg-blue-50 rounded-lg text-sm font-medium">
                                <i class="fas fa-eye mr-1"></i> View
                            </button>
                            <a href="{{ route('admin.rooms.edit', $room->id) }}"
                                class="px-3 py-2 text-indigo-600 hover:bg-indigo-50 rounded-lg text-sm font-medium">
                                <i class="fas fa-edit mr-1"></i> Edit
                            </a>
                        </div>
                        <button onclick="confirmDelete('room', {{ $room->id }})"
                            class="px-3 py-2 text-red-600 hover:bg-red-50 rounded-lg text-sm font-medium">
                            <i class="fas fa-trash mr-1"></i> Delete
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-3">
                <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                    <i class="fas fa-door-open text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No rooms found</h3>
                    <p class="text-gray-500 mb-6">Get started by adding your first room</p>
                    <a href="{{ route('admin.rooms.create') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i> Add Room
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($rooms->hasPages())
        <div class="mt-6">
            {{ $rooms->links() }}
        </div>
    @endif

    <!-- View Modal -->
    <div id="roomModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl w-full max-w-4xl">
                <div class="p-6 border-b flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-800" id="modalTitle">Room Details</h3>
                    <button onclick="closeRoomModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6" id="roomDetails">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function viewRoom(id) {
            fetch(`/admin/rooms/${id}`)
                .then(response => response.json())
                .then(data => {
                    const room = data;
                    document.getElementById('modalTitle').textContent = `Room: ${room.name}`;
                    document.getElementById('roomDetails').innerHTML = `
                <div class="space-y-6">
                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">BASIC INFORMATION</h4>
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center">
                                        <i class="fas fa-door-open text-blue-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-500">Room Name</p>
                                        <p class="font-medium">${room.name}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-green-50 flex items-center justify-center">
                                        <i class="fas fa-map-marker-alt text-green-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-500">Location</p>
                                        <p class="font-medium">${room.location}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center">
                                        <i class="fas fa-users text-purple-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-500">Capacity</p>
                                        <p class="font-medium">${room.capacity} people</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">STATUS & CATEGORY</h4>
                            <div class="space-y-4">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-yellow-50 flex items-center justify-center">
                                        <i class="fas fa-tag text-yellow-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-500">Category</p>
                                        <p class="font-medium">${room.category?.name || 'N/A'}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg ${room.availability_status === 'available' ? 'bg-green-50' : 'bg-red-50'} flex items-center justify-center">
                                        <i class="fas ${room.availability_status === 'available' ? 'fa-check-circle text-green-600' : 'fa-times-circle text-red-600'}"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-500">Status</p>
                                        <span class="px-3 py-1 rounded-full text-xs font-medium ${room.availability_status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                            ${room.availability_status.charAt(0).toUpperCase() + room.availability_status.slice(1)}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center">
                                        <i class="fas fa-calendar-alt text-gray-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-500">Created</p>
                                        <p class="font-medium">${new Date(room.created_at).toLocaleDateString()}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    ${room.description ? `
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-3">DESCRIPTION</h4>
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <p class="text-gray-700">${room.description}</p>
                            </div>
                        </div>
                        ` : ''}
                    
                    <!-- Upcoming Bookings -->
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 mb-3">UPCOMING BOOKINGS</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-500 text-sm">Fetching booking data...</p>
                        </div>
                    </div>
                </div>
            `;
                    document.getElementById('roomModal').classList.remove('hidden');
                });
        }

        function searchRooms(query) {
            const url = new URL("{{ route('admin.rooms.search') }}");

            const currentParams = new URLSearchParams(window.location.search);

            if (query) {
                url.searchParams.set('search', query);
            } else {
                url.searchParams.delete('search');
            }

            window.location.href = url.toString();
        }

        function confirmDelete(type, id) {
            if (confirm(`Are you sure you want to delete this ${type}? This action cannot be undone.`)) {
                fetch(`/admin/rooms/${id}`, {
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
                            alert('Failed to delete room');
                        }
                    });
            }
        }

        function closeRoomModal() {
            document.getElementById('roomModal').classList.add('hidden');
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeRoomModal();
        });
    </script>
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endsection
