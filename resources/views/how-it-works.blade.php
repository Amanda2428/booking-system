@extends('layouts.user')

@section('title', 'How It Works')
@section('subtitle', 'Simple 4-Step Process to Book Your Study Room')

@section('breadcrumb')
    <li class="text-gray-500">How It Works</li>
@endsection

@section('header-actions')
    @auth
        @if (auth()->user()->role == 0)
            <a href="#" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <i class="fas fa-calendar-plus mr-2"></i> Start Booking
            </a>
        @endif
    @else
        <a href="{{ route('register') }}"
            class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
            <i class="fas fa-user-plus mr-2"></i> Sign Up to Book
        </a>
    @endauth
@endsection

@section('content')
    <!-- Steps Overview -->
    <div class="mb-12">
        <div class="relative">
            <!-- Steps Line -->
            <div class="hidden lg:block absolute top-1/2 left-0 right-0 h-1 bg-gray-200 transform -translate-y-1/2"></div>

            <div class="relative grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div class="relative">
                    <div
                        class="w-20 h-20 mx-auto bg-indigo-600 rounded-full flex items-center justify-center mb-6 relative z-10">
                        <span class="text-white text-2xl font-bold">1</span>
                    </div>
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Create Account</h3>
                        <p class="text-gray-600">
                            Sign up with your university email address. Verification is quick and easy.
                        </p>
                        <div class="mt-4">
                            @auth
                                <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">
                                    <i class="fas fa-check mr-1"></i> Completed
                                </span>
                            @else
                                <a href="{{ route('register') }}"
                                    class="inline-block px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 text-sm">
                                    Sign Up Now
                                </a>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="relative">
                    <div
                        class="w-20 h-20 mx-auto bg-indigo-600 rounded-full flex items-center justify-center mb-6 relative z-10">
                        <span class="text-white text-2xl font-bold">2</span>
                    </div>
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Browse & Select</h3>
                        <p class="text-gray-600">
                            Explore available rooms, check real-time availability, and select your preferred study space.
                        </p>
                        <div class="mt-4">
                            <a href="{{ route('user.rooms') }}"
                                class="inline-block px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 text-sm">
                                Browse Rooms
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="relative">
                    <div
                        class="w-20 h-20 mx-auto bg-indigo-600 rounded-full flex items-center justify-center mb-6 relative z-10">
                        <span class="text-white text-2xl font-bold">3</span>
                    </div>
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Book Time Slot</h3>
                        <p class="text-gray-600">
                            Choose your date, time, and duration. Provide booking purpose if needed.
                        </p>
                        <div class="mt-4">
                            @auth
                                @if (auth()->user()->role == 0)
                                    <a href="{{ route('user.bookings.create') }}"
                                        class="inline-block px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg hover:bg-indigo-200 text-sm">
                                        Book Now
                                    </a>
                                @else
                                    <span class="text-sm text-gray-500">Admin account</span>
                                @endif
                            @else
                                <span class="text-sm text-gray-500">Sign in to book</span>
                            @endauth
                        </div>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="relative">
                    <div
                        class="w-20 h-20 mx-auto bg-indigo-600 rounded-full flex items-center justify-center mb-6 relative z-10">
                        <span class="text-white text-2xl font-bold">4</span>
                    </div>
                    <div class="text-center">
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Get Approved</h3>
                        <p class="text-gray-600">
                            Receive confirmation, get notifications, and enjoy your study session.
                        </p>
                        <div class="mt-4">
                            <span class="px-3 py-1 bg-blue-100 text-blue-800 text-sm rounded-full">
                                Auto-Approval
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Steps -->
    <div class="space-y-12">
        <!-- Step 1 Details -->
        <div class="bg-white rounded-xl shadow-sm border p-8 mt-8 mb-8">
            <div class="flex items-start">
                <div class="w-16 h-16 rounded-xl bg-indigo-100 flex items-center justify-center mr-6">
                    <i class="fas fa-user-plus text-indigo-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Step 1: Create Your Account</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Registration Process</h4>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>Use your university email address</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>Complete your profile information</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>Email verification required</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                    <span>Set up your preferences</span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Account Benefits</h4>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-start">
                                    <i class="fas fa-star text-yellow-500 mr-2 mt-1"></i>
                                    <span>Track all your bookings in one place</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-star text-yellow-500 mr-2 mt-1"></i>
                                    <span>Receive booking notifications</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-star text-yellow-500 mr-2 mt-1"></i>
                                    <span>Save favorite rooms for quick booking</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-star text-yellow-500 mr-2 mt-1"></i>
                                    <span>View booking history and analytics</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 2 Details -->
        <div class="bg-white rounded-xl shadow-sm border p-8 mt-8 mb-8">
            <div class="flex items-start">
                <div class="w-16 h-16 rounded-xl bg-blue-100 flex items-center justify-center mr-6">
                    <i class="fas fa-door-open text-blue-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Step 2: Browse & Select Rooms</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Search Filters</h4>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-start">
                                    <i class="fas fa-search text-blue-500 mr-2 mt-1"></i>
                                    <span>Filter by location and building</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-users text-blue-500 mr-2 mt-1"></i>
                                    <span>Search by capacity requirements</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-calendar text-blue-500 mr-2 mt-1"></i>
                                    <span>Check availability by date</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-tag text-blue-500 mr-2 mt-1"></i>
                                    <span>Browse by room type/category</span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Room Details</h4>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-start">
                                    <i class="fas fa-info-circle text-blue-500 mr-2 mt-1"></i>
                                    <span>View photos and descriptions</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-wifi text-blue-500 mr-2 mt-1"></i>
                                    <span>Check available amenities</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-map-marker-alt text-blue-500 mr-2 mt-1"></i>
                                    <span>See exact location on map</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-star text-blue-500 mr-2 mt-1"></i>
                                    <span>Read user reviews and ratings</span>
                                </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Availability</h4>
                            <ul class="space-y-2 text-gray-600">
                                <li class="flex items-start">
                                    <i class="fas fa-clock text-blue-500 mr-2 mt-1"></i>
                                    <span>Real-time availability status</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-calendar-check text-blue-500 mr-2 mt-1"></i>
                                    <span>View booked time slots</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-bell text-blue-500 mr-2 mt-1"></i>
                                    <span>Set availability alerts</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-heart text-blue-500 mr-2 mt-1"></i>
                                    <span>Save rooms to favorites</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3 Details -->
        <div class="bg-white rounded-xl shadow-sm border p-8 mt-8 mb-8">
            <div class="flex items-start">
                <div class="w-16 h-16 rounded-xl bg-green-100 flex items-center justify-center mr-6">
                    <i class="fas fa-calendar-alt text-green-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Step 3: Make Your Booking</h3>
                    <div class="space-y-6">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3">Booking Options</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h5 class="font-medium text-gray-900 mb-2">Quick Booking</h5>
                                    <p class="text-sm text-gray-600">Book available slots with one click for immediate
                                        needs.</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h5 class="font-medium text-gray-900 mb-2">Advanced Booking</h5>
                                    <p class="text-sm text-gray-600">Schedule bookings up to 30 days in advance.</p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3">Booking Rules</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="text-center p-3 bg-blue-50 rounded-lg">
                                    <div class="text-2xl font-bold text-blue-600">4 hrs</div>
                                    <div class="text-sm text-gray-600">Max duration</div>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg">
                                    <div class="text-2xl font-bold text-green-600">30 days</div>
                                    <div class="text-sm text-gray-600">Advance booking</div>
                                </div>
                                <div class="text-center p-3 bg-purple-50 rounded-lg">
                                    <div class="text-2xl font-bold text-purple-600">3</div>
                                    <div class="text-sm text-gray-600">Max active bookings</div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-800 mb-2">Booking Process</h4>
                            <ol class="space-y-3 text-gray-600">
                                <li class="flex items-start">
                                    <span
                                        class="bg-indigo-100 text-indigo-800 rounded-full w-6 h-6 flex items-center justify-center text-sm mr-3">1</span>
                                    <span>Select date and time slot</span>
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="bg-indigo-100 text-indigo-800 rounded-full w-6 h-6 flex items-center justify-center text-sm mr-3">2</span>
                                    <span>Enter purpose (optional)</span>
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="bg-indigo-100 text-indigo-800 rounded-full w-6 h-6 flex items-center justify-center text-sm mr-3">3</span>
                                    <span>Review booking details</span>
                                </li>
                                <li class="flex items-start">
                                    <span
                                        class="bg-indigo-100 text-indigo-800 rounded-full w-6 h-6 flex items-center justify-center text-sm mr-3">4</span>
                                    <span>Submit booking request</span>
                                </li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 4 Details -->
        <div class="bg-white rounded-xl shadow-sm border p-8">
            <div class="flex items-start">
                <div class="w-16 h-16 rounded-xl bg-purple-100 flex items-center justify-center mr-6">
                    <i class="fas fa-check-circle text-purple-600 text-2xl"></i>
                </div>
                <div class="flex-1">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Step 4: Tracking & Session Usage</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3">Monitoring Your Request</h4>
                            <div class="space-y-4 mt-3">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-sync text-green-600"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-900">Live Status Updates</h5>
                                        <p class="text-sm text-gray-600">Track your booking status (Pending, Approved, or
                                            Rejected) in real-time via your personal dashboard.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-history text-blue-600"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-900">Booking Archive</h5>
                                        <p class="text-sm text-gray-600">Access your full history of study sessions to keep
                                            track of your most productive locations.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-exclamation-circle text-amber-600"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-900">Instant Approval</h5>
                                        <p class="text-sm text-gray-600">Many rooms feature auto-approval, allowing you to
                                            secure your spot immediately after booking.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="font-semibold text-gray-800 mb-3">During & After Session</h4>
                            <div class="space-y-4 mt-3">
                                <div class="flex items-start">
                                    <div
                                        class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-id-card text-indigo-600"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-900">Digital Confirmation</h5>
                                        <p class="text-sm text-gray-600">Simply show your approved booking screen on your
                                            mobile device if requested by staff.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-user-shield text-red-600"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-900">Fair Use Policy</h5>
                                        <p class="text-sm text-gray-600">Help the community by cancelling bookings you
                                            can't attend to free up space for others.</p>
                                    </div>
                                </div>
                                <div class="flex items-start">
                                    <div
                                        class="w-10 h-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                                        <i class="fas fa-comment-dots text-purple-600"></i>
                                    </div>
                                    <div>
                                        <h5 class="font-medium text-gray-900">Rate Your Experience</h5>
                                        <p class="text-sm text-gray-600">Once your session ends, leave a rating and
                                            feedback to help other students find the best spots.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="mt-12 bg-gray-50 rounded-xl p-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-6">Frequently Asked Questions</h3>
        <div class="space-y-4">
            <div class="bg-white p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-2">Who can book rooms?</h4>
                <p class="text-gray-600">All registered university students, faculty, and staff with valid university email
                    addresses can book rooms.</p>
            </div>
            <div class="bg-white p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-2">How far in advance can I book?</h4>
                <p class="text-gray-600">You can book rooms up to 30 days in advance. Same-day bookings are also available.
                </p>
            </div>
            <div class="bg-white p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-2">What if I need to cancel my booking?</h4>
                <p class="text-gray-600">You can cancel bookings up to 2 hours before the scheduled time through your
                    dashboard.</p>
            </div>
            <div class="bg-white p-4 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-2">Are there any booking limits?</h4>
                <p class="text-gray-600">Yes, you can have up to 3 active bookings at a time, with a maximum duration of 4
                    hours per booking.</p>
            </div>
        </div>
    </div>

    <!-- CTA -->
    <div class="mt-12 text-center">
        <h3 class="text-2xl font-bold text-gray-900 mb-4">Ready to Book Your First Room?</h3>
        <p class="text-gray-600 mb-8 max-w-2xl mx-auto">Join thousands of students who use StudySpace for their study room
            bookings.</p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @auth
                @if (auth()->user()->role == 0)
                    <a href="{{ route('user.bookings.create') }}" class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                        <i class="fas fa-calendar-plus mr-2"></i> Book a Room Now
                    </a>
                @endif
            @else
                <a href="{{ route('register') }}"
                    class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                    <i class="fas fa-user-plus mr-2"></i> Sign Up to Book
                </a>
                <a href="{{route('user.rooms') }}"
                    class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                    <i class="fas fa-door-open mr-2"></i> Browse Rooms First
                </a>
            @endauth
        </div>
    </div>
@endsection
