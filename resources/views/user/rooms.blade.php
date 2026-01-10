@extends('layouts.user')

@section('title', 'Available Study Rooms')
@section('subtitle', 'Browse and explore our study spaces')

@section('breadcrumb')
    <li><a href="{{ route('welcome') }}">Home</a></li>
    <li class="text-gray-500">Rooms</li>
@endsection

@section('header-actions')
    <div class="flex items-center space-x-3">
        @auth
            @if (auth()->user()->role == 0)
                <a href="{{ route('user.bookings.create') }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                    <i class="fas fa-plus mr-2"></i> Book a Room
                </a>
            @endif
        @else
            <a href="{{ route('login') }}"
                class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center">
                <i class="fas fa-sign-in-alt mr-2"></i> Sign in to Book
            </a>
        @endauth
        <a href="#"
            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center">
            <i class="fas fa-tags mr-2"></i> View Room Types
        </a>
    </div>
@endsection

@section('content')
    <!-- Search and Filters -->
    <div class="mb-8 bg-white rounded-xl shadow-sm border p-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" id="searchInput" placeholder="Search rooms by name, location, or description..."
                        class="w-full pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        value="{{ request('search') }}">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <div class="flex flex-wrap gap-3">
                <select id="categoryFilter"
                    class="border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Categories</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <select id="capacityFilter"
                    class="border rounded-lg px-7 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Any Capacity</option>
                    <option value="1" {{ request('capacity') == '1' ? 'selected' : '' }}>1 Person</option>
                    <option value="2" {{ request('capacity') == '2' ? 'selected' : '' }}>2 People</option>
                    <option value="3" {{ request('capacity') == '3' ? 'selected' : '' }}>3 People</option>
                    <option value="4" {{ request('capacity') == '4' ? 'selected' : '' }}>4 People</option>
                    <option value="5" {{ request('capacity') == '5' ? 'selected' : '' }}>5 People</option>
                    <option value="6" {{ request('capacity') == '6' ? 'selected' : '' }}>6 People</option>
                    <option value="7" {{ request('capacity') == '7' ? 'selected' : '' }}>7 People</option>
                    <option value="8" {{ request('capacity') == '8' ? 'selected' : '' }}>8 People</option>
                    <option value="9" {{ request('capacity') == '9' ? 'selected' : '' }}>9 People</option>
                    <option value="10" {{ request('capacity') == '10' ? 'selected' : '' }}>10 People</option>
                    <option value="21" {{ request('capacity') == '21' ? 'selected' : '' }}>10+ People</option>
                </select>

                <button onclick="applyFilters()"
                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                    <i class="fas fa-filter mr-2"></i> Apply Filters
                </button>

                @if (request()->anyFilled(['search', 'category', 'capacity']))
                    <button onclick="clearFilters()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Clear
                    </button>
                @endif
            </div>
        </div>

        <!-- Active Filters -->
        @if (request()->anyFilled(['search', 'category', 'capacity']))
            <div class="mt-4 flex flex-wrap gap-2">
                @if (request('search'))
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm flex items-center">
                        Search: "{{ request('search') }}"
                        <button onclick="removeFilter('search')" class="ml-2 text-blue-600 hover:text-blue-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                @endif

                @if (request('category'))
                    @php $category = \App\Models\RoomType::find(request('category')); @endphp
                    @if ($category)
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm flex items-center">
                            Category: {{ $category->name }}
                            <button onclick="removeFilter('category')" class="ml-2 text-green-600 hover:text-green-800">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    @endif
                @endif

                @if (request('capacity'))
                    <span class="px-3 py-1 bg-purple-100 text-purple-800 rounded-full text-sm flex items-center">
                        Capacity: {{ request('capacity') == '21' ? '10+ People' : request('capacity') . ' People' }}
                        <button onclick="removeFilter('capacity')" class="ml-2 text-purple-600 hover:text-purple-800">
                            <i class="fas fa-times"></i>
                        </button>
                    </span>
                @endif
            </div>
        @endif
    </div>

    <!-- Rooms Grid -->
    @if ($rooms->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach ($rooms as $room)
                <div
                    class="bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-md transition-shadow relative">
                    <!-- Room Status Badge -->
                    <div class="absolute top-4 right-4 z-10">
                        <span
                            class="px-3 py-1 rounded-full text-xs font-medium 
                        {{ $room->availability_status == 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ ucfirst($room->availability_status) }}
                        </span>
                    </div>

                    <!-- Room Icon -->
                    <div class="h-48 bg-gradient-to-r from-blue-50 to-indigo-50 flex items-center justify-center">
                        <div class="text-center">
                            <div
                                class="w-20 h-20 rounded-xl bg-white shadow-sm flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-door-open text-indigo-600 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900">{{ $room->name }}</h3>
                            <p class="text-gray-600">{{ $room->location }}</p>
                        </div>
                    </div>

                    <!-- Room Details -->
                    <div class="p-6">
                        <div class="space-y-4">
                            <!-- Capacity -->
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-users text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Capacity</p>
                                    <p class="font-medium">{{ $room->capacity }} people</p>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center mr-3">
                                    <i class="fas fa-tag text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Category</p>
                                    <p class="font-medium">{{ $room->category->name ?? 'General' }}</p>
                                </div>
                            </div>

                            <!-- Description -->
                            @if ($room->description)
                                <div>
                                    <p class="text-sm text-gray-500 mb-2">Description</p>
                                    <p class="text-gray-700 line-clamp-2">{{ $room->description }}</p>
                                </div>
                            @endif

                            <!-- Stats -->
                            <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                                <div class="text-center">
                                    <p class="text-lg font-bold text-gray-800">{{ $room->total_bookings ?? 0 }}</p>
                                    <p class="text-xs text-gray-500">Total Bookings</p>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-bold text-gray-800">
                                        {{ number_format($room->avg_rating ?? 0, 1) }}/5</p>
                                    <p class="text-xs text-gray-500">Avg Rating</p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="mt-6 flex justify-between items-center">
                            <button onclick="viewRoomDetails({{ $room->id }})"
                                class="text-indigo-600 hover:text-indigo-800 font-medium">
                                View Details
                            </button>

                            @auth
                                @if (auth()->user()->role == 0)
                                    <a href="{{ route('user.bookings.create') }}?room_id={{ $room->id }}"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                                        Book Now
                                    </a>
                                @else
                                    <span class="text-sm text-gray-500">Admin account</span>
                                @endif
                            @else
                                <a href="{{ route('login') }}"
                                    class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">
                                    Sign in to Book
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($rooms->hasPages())
            <div class="mt-8">
                {{ $rooms->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-16 bg-white rounded-xl shadow-sm border">
            <i class="fas fa-door-closed text-5xl text-gray-300 mb-6"></i>
            <h3 class="text-2xl font-bold text-gray-700 mb-3">No rooms found</h3>
            <p class="text-gray-600 mb-8 max-w-md mx-auto">
                @if (request()->anyFilled(['search', 'category', 'capacity']))
                    No rooms match your current filters. Try adjusting your search criteria.
                @else
                    There are no rooms available at the moment. Please check back later.
                @endif
            </p>
            @if (request()->anyFilled(['search', 'category', 'capacity']))
                <button onclick="clearFilters()"
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    Clear All Filters
                </button>
            @endif
        </div>
    @endif

    <!-- Room Details Modal -->
    <div id="roomModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
                <div class="p-3 border-b flex justify-between items-center sticky top-0 bg-white">
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
        // Get CSRF token from meta tag
        const csrfTokenMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfTokenMeta ? csrfTokenMeta.getAttribute('content') : '';

        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const category = document.getElementById('categoryFilter').value;
            const capacity = document.getElementById('capacityFilter').value;

            const params = new URLSearchParams();

            if (search) params.set('search', search);
            if (category) params.set('category', category);
            if (capacity) params.set('capacity', capacity);

            const queryString = params.toString();
            window.location.href = queryString ? '{{ route('user.rooms') }}?' + queryString : '{{ route('user.rooms') }}';
        }

        function clearFilters() {
            window.location.href = '{{ route('user.rooms') }}';
        }

        function removeFilter(filterName) {
            const params = new URLSearchParams(window.location.search);
            params.delete(filterName);
            window.location.href = '{{ route('user.rooms') }}?' + params.toString();
        }

        function viewRoomDetails(roomId) {
            // Show loading state
            document.getElementById('roomDetails').innerHTML = `
        <div class="text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-300 mb-4"></i>
            <p class="text-gray-600">Loading room details...</p>
        </div>
    `;
            document.getElementById('roomModal').classList.remove('hidden');

            // Make AJAX request to the correct endpoint
        fetch(`/user/rooms/${roomId}/details`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    if (!response.ok) {
                        if (response.status === 401) {
                            // Unauthorized - session expired
                            window.location.href = '/login';
                            return;
                        }
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Room details data:', data); // Debug log
                    if (data.success) {
                        const room = data.room;

                        let reviewsHTML = '';
                        if (room.recent_feedbacks && room.recent_feedbacks.length > 0) {
                            reviewsHTML = `
        <div class="relative">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-lg font-semibold text-gray-800">Recent Reviews</h4>

                ${room.recent_feedbacks.length > 1 ? `
                                <button onclick="scrollReviews('right')"
                                        class="p-2 rounded-full bg-gray-100 hover:bg-gray-200 transition">
                                    <i class="fas fa-chevron-right"></i>
                                </button>
                                ` : ''}
            </div>

            <div id="reviewsContainer"
                 class="flex gap-4 overflow-x-auto scroll-smooth scrollbar-hide pb-2">

                ${room.recent_feedbacks.map(feedback => {
                    const userName = feedback.user ? feedback.user.name : 'Anonymous';
                    const comment = feedback.comment
                        ? `<p class="text-gray-700 mt-3 text-sm line-clamp-3">${feedback.comment}</p>`
                        : '';
                    const adminReply = feedback.admin_reply ? `
                                        <div class="mt-3 p-3 bg-blue-50 rounded-lg border-l-4 border-blue-500 text-sm">
                                            <strong>Admin:</strong> ${feedback.admin_reply}
                                        </div>
                                    ` : '';

                    return `
                                        <div class="min-w-[320px] max-w-[320px] bg-white border rounded-xl p-4 shadow-sm">

                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <p class="font-semibold text-gray-800">${userName}</p>
                                                    <p class="text-xs text-gray-500">
                                                        ${new Date(feedback.created_at).toLocaleDateString()}
                                                    </p>
                                                </div>

                                                <div class="text-right">
                                                    <div class="text-yellow-500 text-sm">
                                                        ${getStars(feedback.rating)}
                                                    </div>
                                                    <p class="text-xs font-medium text-gray-600">
                                                        ${feedback.rating}/5
                                                    </p>
                                                </div>
                                            </div>

                                            ${comment}
                                            ${adminReply}
                                        </div>
                                    `;
                }).join('')}
            </div>
        </div>
    `;
                        }

                        function scrollReviews(direction) {
                            const container = document.getElementById('reviewsContainer');
                            const scrollAmount = 340;

                            container.scrollBy({
                                left: direction === 'right' ? scrollAmount : -scrollAmount,
                                behavior: 'smooth'
                            });
                        }


                        // Build availability HTML
                        let availabilityHTML = '';
                        if (room.today_availability && room.today_availability.length > 0) {
                            availabilityHTML = `
                    
                `;
                        } else {
                            availabilityHTML = `
                    <div>
                        <h4 class="text-lg font-semibold text-gray-800 mb-3">Today's Availability</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="text-gray-600 text-center py-4">No availability data for today.</p>
                        </div>
                    </div>
                `;
                        }

                        document.getElementById('roomDetails').innerHTML = `
                <div class="space-y-8">
                    <!-- Room Header -->
                    <div class="flex items-start">
                        <div class="w-24 h-24 rounded-xl bg-gradient-to-r from-blue-50 to-indigo-50 flex items-center justify-center mr-6">
                            <i class="fas fa-door-open text-indigo-600 text-4xl"></i>
                        </div>
                      <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">${room.name}</h3>

                            <div class="flex items-center mt-2 text-gray-600">
                                <i class="fas fa-map-marker-alt mr-2 text-gray-400"></i>
                                <span>${room.location}</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 justify-start md:justify-end">

                            <!-- Availability -->
                            <span
                                class="px-4 py-2 rounded-full text-sm font-semibold
                                ${room.availability_status === 'available'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : 'bg-rose-100 text-rose-700'}">
                                ${room.availability_status === 'available' ? 'Available' : 'Unavailable'}
                            </span>

                            <!-- Capacity -->
                            <span class="px-4 py-2 bg-blue-100 text-blue-700 text-sm font-semibold rounded-full">
                                <i class="fas fa-users mr-1"></i> ${room.capacity}
                            </span>

                            <!-- Category -->
                            ${room.category ? `
                                            <span class="px-4 py-2 bg-purple-100 text-purple-700 text-sm font-semibold rounded-full">
                                                <i class="fas fa-tag mr-1"></i> ${room.category.name}
                                            </span>
                                            ` : ''}
                        </div>
                    </div>

                    </div>
                    
                    <!-- Description -->
                    ${room.description ? `
                                    <div>
                                        <h4 class="text-md font-semibold text-gray-800 ">Description</h4>
                                        <div class="p-2 bg-gray-50 rounded-lg">
                                            <p class="text-gray-700 whitespace-pre-line">${room.description}</p>
                                        </div>
                                    </div>
                                    ` : ''}
                    
                    <!-- Room Statistics -->
                    <div>
                        <h4 class="text-md font-semibold text-gray-800 mb-1">Room Statistics</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-gray-800">${room.total_bookings || 0}</div>
                                <div class="text-sm text-gray-600">Total Bookings</div>
                            </div>
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <div class="text-2xl font-bold text-gray-800">${room.avg_rating ? room.avg_rating.toFixed(1) : '0.0'}/5</div>
                                <div class="text-sm text-gray-600">Average Rating</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Availability -->
                    ${availabilityHTML}
                    
                    <!-- Reviews -->
                    ${reviewsHTML}
                    
                    <!-- Quick Book -->
                    <div class="sticky bottom-0 bg-white pt-6 border-t">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-semibold text-gray-800">Ready to book this room?</h4>
                                <p class="text-sm text-gray-600">Select a date and time to continue</p>
                            </div>
                            <div class="flex space-x-3">
                                <button onclick="closeRoomModal()" 
                                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                                    Close
                                </button>
                                <a href="/user/bookings/create?room_id=${room.id}"
                                   class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                                    <i class="fas fa-calendar-plus mr-2"></i> Book This Room
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
                    } else {
                        throw new Error('Failed to load room details');
                    }
                })
                .catch(err => {
                    console.error('Error loading room details:', err);
                    document.getElementById('roomDetails').innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-exclamation-circle text-4xl text-red-300 mb-4"></i>
                <p class="text-red-600">Error loading room details. Please try again.</p>
                <p class="text-sm text-gray-500 mt-2">${err.message}</p>
            </div>
        `;
                });
        }

        function getStars(rating) {
            let stars = '';
            const fullStars = Math.floor(rating);
            const hasHalfStar = rating % 1 >= 0.5;

            for (let i = 1; i <= 5; i++) {
                if (i <= fullStars) {
                    stars += '<i class="fas fa-star"></i>';
                } else if (i === fullStars + 1 && hasHalfStar) {
                    stars += '<i class="fas fa-star-half-alt"></i>';
                } else {
                    stars += '<i class="far fa-star"></i>';
                }
            }
            return stars;
        }

        function closeRoomModal() {
            document.getElementById('roomModal').classList.add('hidden');
        }

        // Initialize search with Enter key
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                applyFilters();
            }
        });

        // Close modal on ESC key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeRoomModal();
        });

        // Initialize filters from URL
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const search = urlParams.get('search');
            const category = urlParams.get('category');
            const capacity = urlParams.get('capacity');

            if (search) document.getElementById('searchInput').value = search;
            if (category) document.getElementById('categoryFilter').value = category;
            if (capacity) document.getElementById('capacityFilter').value = capacity;
        });
    </script>
@endsection
