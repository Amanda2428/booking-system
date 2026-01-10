@extends('layouts.user')

@section('title', 'Welcome to StudySpace')
@section('subtitle', 'Book Your Perfect Study Space')

@section('header-actions')
    @auth
        @if (auth()->user()->role == 0)
            <a href="#" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <i class="fas fa-plus mr-2"></i> Book a Room
            </a>
        @endif
    @else
        <a href="{{ route('register') }}"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
            <i class="fas fa-user-plus mr-2"></i> Sign Up Free
        </a>
    @endauth
@endsection

@section('content')
    <!-- Hero Section -->
    <div x-data="{
        images: [
            '/images/library1.jpg',
            '/images/library2.jpg',
            '/images/library3.jpg'
        ],
        current: 0,
        init() {
            setInterval(() => {
                this.current = (this.current + 1) % this.images.length;
            }, 5000);
        }
    }"
        class="relative rounded-2xl overflow-hidden mb-16
       h-[520px] sm:h-[600px] lg:h-[680px]">

        <!-- Background Images -->
        <template x-for="(image, index) in images" :key="index">
            <div x-show="current === index" x-transition:enter="transition-opacity duration-1000"
                x-transition:leave="transition-opacity duration-1000"
                class="absolute inset-0 bg-cover bg-center scale-105 animate-[zoom_18s_ease-in-out_infinite]"
                :style="`background-image: url('${image}')`"></div>
        </template>

        <!-- Overlay -->
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-700/80 via-indigo-600/70 to-purple-700/80"></div>
        <div class="absolute inset-0 bg-black/20"></div>

        <!-- Content -->
        <div class="relative z-10 flex items-center justify-center h-full px-6 text-center">
            <div class="max-w-4xl">

                <!-- Badge -->
                <span class="inline-block mb-6 px-4 py-2 rounded-full bg-white/20 text-white text-sm tracking-wide">
                    📚 Smart Library Booking
                </span>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white mb-6 leading-tight drop-shadow-xl">
                    Study Smarter,<br class="hidden sm:block"> Not Harder
                </h1>

                <p class="text-lg sm:text-xl font-semibold text-white/90 mb-12 leading-relaxed drop-shadow-lg">
                    Reserve library study rooms for group projects, individual sessions,
                    or exam preparation — all managed in one simple platform.
                </p>


                <div class="flex flex-col sm:flex-row gap-5 justify-center">
                    @auth
                        @if (auth()->user()->role == 0)
                            <a href="{{ route('user.bookings.create') }}"
                                class="group px-8 py-3 mt-8 mb-8 bg-white text-indigo-600 rounded-2xl font-semibold
                                  shadow-xl hover:shadow-2xl hover:-translate-y-1
                                  transition-all duration-300">
                                <i class="fas fa-calendar-plus mr-2 group-hover:scale-110 transition"></i>
                                Book a Room Now
                            </a>
                        @endif
                    @else
                        <a href="{{ route('register') }}"
                            class="group px-8 py-3 bg-white text-indigo-600 rounded-2xl font-semibold
                              shadow-xl hover:shadow-2xl hover:-translate-y-1
                              transition-all duration-300">
                            <i class="fas fa-user-plus mr-2 group-hover:scale-110 transition"></i>
                            Get Started Free
                        </a>

                        <a href="{{ route('how-it-works') }}"
                            class="group px-8 py-3 border-2 border-white/80 text-white rounded-2xl font-semibold
                              hover:bg-white hover:text-indigo-600
                              hover:-translate-y-1 transition-all duration-300">
                            <i class="fas fa-play-circle mr-2 group-hover:scale-110 transition"></i>
                            How It Works
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Optional Scroll Indicator -->
        <div class="absolute bottom-6 left-1/2 -translate-x-1/2 text-white/80 animate-bounce">
            <i class="fas fa-chevron-down text-xl"></i>
        </div>
    </div>



    <!-- Statistics Section -->
    <div class="mb-12">
        <h2 class="text-3xl font-bold text-gray-900 text-center mt-8 mb-8">Our Platform at a Glance</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-8 mb-8">
            <div class="bg-white p-6 rounded-xl shadow-sm border text-center">
                <div class="text-4xl font-bold text-indigo-600 mb-2">
                    {{ number_format($totalRooms) }}
                </div>
                <div class="font-medium text-gray-900">Study Rooms</div>
                <div class="text-sm text-gray-500">Available for booking</div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border text-center">
                <div class="text-4xl font-bold text-indigo-600 mb-2">
                    {{ number_format($totalBookings) }}
                </div>
                <div class="font-medium text-gray-900">Bookings Made</div>
                <div class="text-sm text-gray-500">Total reservations</div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border text-center">
                <div class="text-4xl font-bold text-indigo-600 mb-2">
                    {{ number_format($activeUsers) }}
                </div>
                <div class="font-medium text-gray-900">Active Users</div>
                <div class="text-sm text-gray-500">Students & Researchers</div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border text-center">
                <div class="text-4xl font-bold text-indigo-600 mb-2">
                    {{ number_format($approvedBookings) }}
                </div>
                <div class="font-medium text-gray-900">Approved Bookings</div>
                <div class="text-sm text-gray-500">Successfully completed</div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <a href="{{ route('user.rooms') }}"
            class="bg-white p-6 rounded-xl shadow-sm border hover:shadow-md transition-shadow hover:border-indigo-300">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-4">
                    <i class="fas fa-door-open text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Browse Rooms</h3>
                    <p class="text-sm text-gray-600">Explore available study spaces</p>
                </div>
            </div>
            <p class="text-gray-700">View all available study rooms with detailed information about capacity, amenities, and
                location.</p>
        </a>

        <a href="#"
            class="bg-white p-6 rounded-xl shadow-sm border hover:shadow-md transition-shadow hover:border-indigo-300">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center mr-4">
                    <i class="fas fa-tags text-purple-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">Room Types</h3>
                    <p class="text-sm text-gray-600">Different categories available</p>
                </div>
            </div>
            <p class="text-gray-700">Learn about the different types of study rooms available, from individual pods to group
                collaboration spaces.</p>
        </a>

        <a href="{{ route('how-it-works') }}"
            class="bg-white p-6 rounded-xl shadow-sm border hover:shadow-md transition-shadow hover:border-indigo-300">
            <div class="flex items-center mb-4">
                <div class="w-12 h-12 rounded-lg bg-green-100 flex items-center justify-center mr-4">
                    <i class="fas fa-play-circle text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-gray-900">How It Works</h3>
                    <p class="text-sm text-gray-600">Simple 4-step process</p>
                </div>
            </div>
            <p class="text-gray-700">Learn how easy it is to book a study room in just a few simple steps.</p>
        </a>
    </div>

    <!-- Featured Rooms -->
    <div class="mt-8 mb-12">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-900">Featured Study Rooms</h2>
            <a href="{{ route('user.rooms') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                View All Rooms →
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($featuredRooms as $room)
                <div class="bg-white rounded-xl shadow-sm border overflow-hidden hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $room->name }}</h3>
                                <p class="text-gray-600">{{ $room->location }}</p>
                            </div>
                            <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                                Available
                            </span>
                        </div>

                        <div class="space-y-3 mb-4">
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-users mr-3 w-5"></i>
                                <span>Capacity: {{ $room->capacity }} people</span>
                            </div>
                            <div class="flex items-center text-gray-600">
                                <i class="fas fa-tag mr-3 w-5"></i>
                                <span>{{ $room->category->name ?? 'General' }}</span>
                            </div>
                        </div>

                        @if ($room->description)
                            <p class="text-gray-700 mb-6 line-clamp-2">{{ $room->description }}</p>
                        @endif

                        <div class="flex justify-between items-center">
                            @auth
                                @if (auth()->user()->role == 0)
                                    <a href="{{ route('user.bookings.create', ['room_id' => $room->id]) }}"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm">
                                        Book This Room
                                    </a>
                                @else
                                    <span class="text-sm text-gray-500">Contact admin for booking</span>
                                @endif
                            @else
                                <span class="text-sm text-gray-500">
                                    <a href="{{ route('login') }}" class="text-indigo-600 hover:underline">Sign in</a> to
                                    book
                                </span>
                            @endauth

                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- CTA Section -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-500 rounded-2xl p-8 text-center">
        <h2 class="text-3xl font-bold text-white mb-4">Ready to Boost Your Productivity?</h2>
        <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
            Join thousands of students and researchers who trust StudySpace for their study room bookings.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @auth
                @if (auth()->user()->role == 0)
                    <a href="{{ route('user.bookings.create') }}"
                        class="px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-gray-100 font-medium">
                        <i class="fas fa-calendar-plus mr-2"></i> Book Your First Room
                    </a>
                @endif
            @else
                <a href="{{ route('register') }}"
                    class="px-6 py-3 bg-white text-indigo-600 rounded-lg hover:bg-black  hover:-translate-y-1
                              transition-all duration-300 font-medium">
                    <i class="fas fa-user-plus mr-2"></i> Sign Up Free
                </a>
                <a href="{{ route('login') }}"
                    class="px-6 py-3 border-2 border-white text-white rounded-lg hover:bg-white hover:text-indigo-600 font-medium">
                    <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                </a>
            @endauth
        </div>
        <p class="mt-4 text-sm text-indigo-200">
            No credit card required. Free for all university students and staff.
        </p>
    </div>
@endsection

@section('scripts')
    <script>
        // Line clamp utility
        const style = document.createElement('style');
        style.textContent = `
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    `;
        document.head.appendChild(style);
    </script>
@endsection
