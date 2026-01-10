<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - StudySpace</title>

    <!-- Icons & Alpine -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @vite('resources/css/app.css')

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="bg-gray-50">

    <!-- NAVIGATION -->
    <nav class="bg-white shadow-lg" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">

                <!-- LOGO -->
                <div class="flex items-center">
                    <a href="{{ route('welcome') }}" class="flex items-center">
                        <div
                            class="w-12 h-12 flex items-center justify-center overflow-hidden rounded-full bg-white ring-1 ring-gray-200">
                            <img src="{{ asset('storage/logo/logo.png') }}" alt="StudySpace Logo"
                                class="w-full h-full object-contain">
                        </div>

                        <span class="ml-3 text-xl font-bold text-gray-800">StudySpace</span>
                    </a>
                </div>

                <!-- DESKTOP MENU -->
                <ul class="hidden md:flex items-center gap-3">
                    <li>
                        <a href="{{ route('welcome') }}"
                            class="px-4 py-2 rounded-lg transition
           {{ request()->routeIs('welcome')
               ? 'bg-indigo-50 text-indigo-600 font-semibold'
               : 'text-gray-700 hover:bg-gray-100 hover:text-indigo-600' }}">
                            Home
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('how-it-works') }}"
                            class="px-4 py-2 rounded-lg transition
           {{ request()->routeIs('how-it-works')
               ? 'bg-indigo-50 text-indigo-600 font-semibold'
               : 'text-gray-700 hover:bg-gray-100 hover:text-indigo-600' }}">
                            How It Works
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('user.rooms') }}"
                            class="px-4 py-2 rounded-lg transition
           {{ request()->routeIs('rooms.*')
               ? 'bg-indigo-50 text-indigo-600 font-semibold'
               : 'text-gray-700 hover:bg-gray-100 hover:text-indigo-600' }}">
                            Rooms
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('room-types') }}"
                            class="px-4 py-2 rounded-lg transition
           {{ request()->routeIs('room-types')
               ? 'bg-indigo-50 text-indigo-600 font-semibold'
               : 'text-gray-700 hover:bg-gray-100 hover:text-indigo-600' }}">
                            Room Types
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('feedback') }}"
                            class="px-4 py-2 rounded-lg transition
           {{ request()->routeIs('feedback.*')
               ? 'bg-indigo-50 text-indigo-600 font-semibold'
               : 'text-gray-700 hover:bg-gray-100 hover:text-indigo-600' }}">
                            Feedback
                        </a>
                    </li>


                    <!-- AUTH SEPARATOR -->
                    <li class="ml-6 border-l pl-6 flex items-center">
                        @auth
                            <!-- USER DROPDOWN (PRETTIER) -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="flex items-center gap-2 px-3 py-2 rounded-full
                               hover:bg-gray-100 transition">
                                    <div class="w-9 h-9 rounded-full bg-indigo-100 flex items-center justify-center">
                                        @if (auth()->user()->profile)
                                            <img src="{{ asset('storage/' . auth()->user()->profile) }}"
                                                class="w-9 h-9 rounded-full object-cover">
                                        @else
                                            <i class="fas fa-user text-indigo-600"></i>
                                        @endif
                                    </div>
                                    <span class="font-medium text-gray-700">
                                        {{ auth()->user()->name }}
                                    </span>
                                    <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                                </button>

                                <ul x-show="open" x-transition @click.away="open = false"
                                    class="absolute right-0 mt-3 w-52 bg-white rounded-xl shadow-lg border overflow-hidden z-50">
                                    <li>
                                        <a href="{{ route('user.dashboard') }}" class="block px-4 py-3 hover:bg-gray-50">
                                            Dashboard
                                        </a>
                                    </li>
                                    <li>
                                        <a href="{{ route('profile.edit') }}" class="block px-4 py-3 hover:bg-gray-50">
                                            My Profile
                                        </a>
                                    </li>
                                    <li class="border-t">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button class="w-full text-left px-4 py-3 text-red-600 hover:bg-red-50">
                                                Logout
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        @else
                            <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg hover:bg-gray-100 transition">
                                Log in
                            </a>
                            <a href="{{ route('register') }}"
                                class="ml-2 px-5 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                                Sign up
                            </a>
                        @endauth
                    </li>

                    <li class="ml-6 border-l pl-6 flex items-center">
                        @auth
                            <div class="relative mr-4">
                                <a href="{{ route('user.feedback.index') }}"
                                    class="p-2 text-gray-400 hover:text-indigo-600 transition flex items-center justify-center rounded-full hover:bg-gray-100">
                                    <i class="fas fa-bell text-lg"></i>
                                    <span
                                        class="absolute top-1 right-1 block h-2.5 w-2.5 rounded-full bg-red-500 ring-2 ring-white"></span>
                                </a>
                            </div>

                            <div class="relative" x-data="{ open: false }">
                            </div>
                        @else
                        @endauth
                    </li>
                </ul>


                <!-- MOBILE BUTTON -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden text-gray-700">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>

        <!-- MOBILE MENU -->
        <div x-show="mobileMenuOpen" x-transition x-cloak @click.away="mobileMenuOpen = false"
            class="md:hidden border-t bg-white">

            <ul class="px-4 py-3 space-y-1">
                <li><a href="{{ route('welcome') }}" @click="mobileMenuOpen=false"
                        class="block px-3 py-2 hover:bg-gray-100">Home</a></li>
                <li><a href="{{ route('how-it-works') }}" @click="mobileMenuOpen=false"
                        class="block px-3 py-2 hover:bg-gray-100">How It
                        Works</a></li>
                <li><a href="#" @click="mobileMenuOpen=false" class="block px-3 py-2 hover:bg-gray-100">Rooms</a>
                </li>
                <li><a href="#" @click="mobileMenuOpen=false" class="block px-3 py-2 hover:bg-gray-100">Room
                        Types</a></li>
                <li><a href="{{ route('feedback') }}" @click="mobileMenuOpen=false"
                        class="block px-3 py-2 hover:bg-gray-100">Feedback</a></li>


                <li class="border-t mt-2 pt-2">
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="w-full text-left px-3 py-2 text-red-600 hover:bg-red-50">
                                Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="block px-3 py-2 hover:bg-gray-100">Log in</a>
                        <a href="{{ route('register') }}" class="block px-3 py-2 bg-indigo-600 text-white rounded-md mt-1">
                            Sign up
                        </a>
                    @endauth
                </li>
            </ul>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <div class="max-w-7xl mx-auto px-4 py-8">
        @yield('content')
    </div>

    <!-- FOOTER -->
    <footer class="border-t mt-12 py-6 text-center text-sm text-gray-500">
        © {{ date('Y') }} StudySpace. All rights reserved.
    </footer>

    @yield('scripts')
</body>

</html>
