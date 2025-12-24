<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Library Room Booking Admin</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @vite('resources/css/app.css')
</head>

<body class="bg-gray-50">

<div x-data="{ sidebarOpen: false }" class="flex h-screen">

    <!-- Mobile Menu Button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button @click="sidebarOpen = true"
                class="p-2 bg-indigo-600 text-white rounded-lg">
            <i class="fas fa-bars"></i>
        </button>
    </div>

    <!-- Mobile Backdrop -->
    <div x-show="sidebarOpen"
         @click="sidebarOpen = false"
         x-transition.opacity
         class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden">
    </div>

    <!-- Sidebar -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed lg:static inset-y-0 left-0 z-40 w-64
               bg-white shadow-lg transform transition-transform
               duration-300 lg:translate-x-0 lg:shadow-none">

        <!-- Close button (mobile) -->
        <div class="lg:hidden flex justify-end p-4">
            <button @click="sidebarOpen = false"
                    class="text-gray-600 hover:text-red-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Logo -->
        <div class="p-6 border-b">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-white text-xl"></i>
                </div>
                <div class="ml-3">
                    <h2 class="text-lg font-bold text-gray-800">Room Booking</h2>
                    <p class="text-sm text-gray-500">Admin Panel</p>
                </div>
            </div>
        </div>

        <!-- User Profile -->
        <div class="p-4 border-b">
            <div class="flex items-center">
                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center">
                    <i class="fas fa-user text-indigo-600"></i>
                </div>
                <div class="ml-3">
                    <p class="font-medium text-gray-800">
                        {{ Auth::user()->name ?? 'Admin' }}
                    </p>
                    <p class="text-sm text-gray-500">Administrator</p>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="p-4">
            <ul class="space-y-2">

                <li>
                    <a href="{{ route('admin.dashboard') }}"
                       @click="sidebarOpen = false"
                       class="flex items-center p-3 rounded-lg
                       {{ request()->routeIs('admin.dashboard')
                            ? 'bg-indigo-50 text-indigo-600'
                            : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-home mr-3"></i>
                        Dashboard
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.bookings') }}"
                       @click="sidebarOpen = false"
                       class="flex items-center p-3 rounded-lg
                       {{ request()->routeIs('admin.bookings*')
                            ? 'bg-indigo-50 text-indigo-600'
                            : 'text-gray-700 hover:bg-gray-100' }}">
                        <i class="fas fa-calendar-alt mr-3"></i>
                        Bookings
                        <span class="ml-auto bg-indigo-100 text-indigo-600 text-xs px-2 py-1 rounded-full">
                            {{ \App\Models\Booking::where('status','pending')->count() }}
                        </span>
                    </a>
                </li>

                <li>
                    <a href="#"
                       @click="sidebarOpen = false"
                       class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-door-open mr-3"></i>
                        Rooms
                    </a>
                </li>

                <li>
                    <a href="#"
                       @click="sidebarOpen = false"
                       class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-tags mr-3"></i>
                        Categories
                    </a>
                </li>

                <li>
                    <a href="#"
                       @click="sidebarOpen = false"
                       class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-users mr-3"></i>
                        Users
                    </a>
                </li>

                <li>
                    <a href="#"
                       @click="sidebarOpen = false"
                       class="flex items-center p-3 rounded-lg text-gray-700 hover:bg-gray-100">
                        <i class="fas fa-comment-alt mr-3"></i>
                        Feedback
                    </a>
                </li>

                <li class="pt-4 mt-4 border-t">
                    <a href="#"
                       @click="sidebarOpen = false"
                       class="flex items-center p-3 rounded-lg text-red-600 hover:bg-red-50">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        Logout
                    </a>
                </li>

            </ul>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Header -->
        <header class="bg-white shadow-sm border-b">
            <div class="flex justify-between items-center px-6 py-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">@yield('title')</h1>
                    <p class="text-gray-600">@yield('subtitle')</p>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto p-6">
            @yield('content')
        </main>

    </div>
</div>

@yield('scripts')

</body>
</html>
