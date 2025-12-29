@extends('layouts.admin')

@section('title', 'Users Management')
@section('subtitle', 'Manage system users and permissions')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="text-gray-500">Users</li>
@endsection

@section('content')
    <!-- Stats Cards -->

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

        <!-- Total Users -->
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</h3>
                    <p class="text-gray-600">Total Users</p>
                </div>
            </div>
        </div>

        <!-- Total Administrators -->
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-shield text-purple-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalAdmins }}</h3>
                    <p class="text-gray-600">Total Administrators</p>
                </div>
            </div>
        </div>

        <!-- Total Students -->
        <div class="bg-white rounded-xl shadow-sm p-6 border">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-user-graduate text-green-600 text-xl"></i>
                </div>
                <div class="ml-4">
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalStudents }}</h3>
                    <p class="text-gray-600">Total Students</p>
                </div>
            </div>
        </div>

    </div>



    <!-- Header with Actions -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Users List</h2>
            <p class="text-gray-600">Manage all system users</p>
        </div>
        <div class="flex items-center space-x-3">
            <!-- Search Input -->
            <div class="relative">
                <input type="text" placeholder="Search users..." value="{{ request('search') }}"
                    onkeypress="if(event.key === 'Enter'){ searchUsers(this.value); }"
                    class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full md:w-64">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>

            <!-- Role Filter -->
            <select onchange="filterByRole(this.value)"
                class="border rounded-lg px-7 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">All Roles</option>
                <option value="0" {{ request('role') == '0' ? 'selected' : '' }}>Student</option>
                <option value="1" {{ request('role') == '1' ? 'selected' : '' }}>Admin</option>
            </select>

            <!-- Add User Button -->
            <button onclick="openCreateModal()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <i class="fas fa-user-plus mr-2"></i> Add User
            </button>
        </div>
    </div>




    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-gray-50 text-left text-sm text-gray-600">
                        <th class="py-3 px-6 font-medium">ID</th>
                        <th class="py-3 px-6 font-medium">User</th>
                        <th class="py-3 px-6 font-medium">Role</th>
                        <th class="py-3 px-6 font-medium">Bookings</th>
                        <th class="py-3 px-6 font-medium">Status</th>
                        <th class="py-3 px-6 font-medium">Active</th>
                        <th class="py-3 px-6 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="py-4 px-6">{{ $user->id }} </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full {{ $user->profile ? 'bg-cover bg-center' : 'bg-indigo-100' }} flex items-center justify-center"
                                        style="{{ $user->profile ? "background-image: url('" . asset('storage/' . $user->profile) . "')" : '' }}">
                                        @if (!$user->profile)
                                            <i class="fas fa-user text-indigo-600"></i>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-medium text-gray-800">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                        <div class="text-xs text-gray-400 mt-1">
                                            Joined {{ $user->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium {{ $user->role == 1 ? 'bg-purple-100 text-purple-800 border border-purple-200' : 'bg-green-100 text-green-800 border border-green-200' }}">
                                    <i class="fas {{ $user->role == 1 ? 'fa-user-shield' : 'fa-user-graduate' }} mr-1"></i>
                                    {{ $user->role == 1 ? 'Administrator' : 'Student' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="text-center">
                                    <p class="text-lg font-bold text-gray-800">{{ $user->total_bookings_count ?? 0 }}</p>
                                    <p class="text-xs text-gray-500">total bookings</p>
                                </div>
                            </td>

                            <td class="py-4 px-6">
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-medium {{ $user->email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    <i
                                        class="fas {{ $user->email_verified_at ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                    {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if ($user->is_online)
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <i class="fas fa-circle mr-1 text-xs"></i>
                                        Online
                                    </span>
                                @else
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <i class="fas fa-circle mr-1 text-xs"></i>
                                        Offline
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-1">
                                    <button onclick="viewUser({{ $user->id }})"
                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                        title="View Profile">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editUser({{ $user->id }})"
                                        class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                        title="Edit User">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if ($user->id != auth()->id())
                                        <div class="relative" x-data="{ open: false }">
                                            <button @click="open = !open"
                                                class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <div x-show="open" @click.away="open = false"
                                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border z-10">

                                                @if (!$user->email_verified_at)
                                                    <a href="#" onclick="verifyUser({{ $user->id }})"
                                                        class="flex items-center px-4 py-2 text-blue-700 hover:bg-blue-50">
                                                        <i class="fas fa-check-circle mr-3"></i> Verify Email
                                                    </a>
                                                @endif
                                                <hr>
                                                <a href="#" onclick="confirmDelete('user', {{ $user->id }})"
                                                    class="flex items-center px-4 py-2 text-red-700 hover:bg-red-50">
                                                    <i class="fas fa-trash mr-3"></i> Delete User
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                    <p class="text-gray-500 font-medium">No users found</p>
                                    <p class="text-gray-400 text-sm mt-1">Try adjusting your filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($users->hasPages())
            <div class="p-6 border-t bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
                    </div>
                    <div class="flex items-center space-x-2">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- View Modal -->
    <div id="userModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl w-full max-w-2xl">
                <div class="p-6 border-b flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-800" id="modalTitle">User Profile</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-6" id="userDetails">
                    <!-- Content loaded via AJAX -->
                </div>
                <div class="p-6 border-t flex justify-end space-x-3">
                    <button onclick="closeModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit User Modal -->
    <div id="editUserModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">>
        <div class="bg-white w-full max-w-md rounded-2xl shadow-xl overflow-hidden">

            <div class="flex items-center justify-between p-6 border-b border-gray-100">
                <div>
                    <h3 id="editModalTitle" class="text-2xl font-bold text-gray-800"></h3>
                    <p class="text-sm text-gray-500 mt-1" id="editModalSubtitle">Fill in the user details</p>
                </div>
                <button onclick="closeEditModal()"
                    class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 p-2 rounded-full transition-colors duration-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <form id="userForm" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="_method" id="methodSpoof" value="POST">
                <input type="hidden" id="userId" name="id">

                <!-- Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Full Name
                    </label>
                    <input id="edit_name" name="name" required placeholder="Enter full name"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address
                    </label>
                    <input id="edit_email" name="email" type="email" required placeholder="example@email.com"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <!-- Role -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Role
                    </label>
                    <select id="edit_role" name="role"
                        class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="0">Student</option>
                        <option value="1">Admin</option>
                    </select>
                </div>

                <!-- Password -->
                <div id="passwordFields" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Password
                        </label>
                        <input type="password" id="edit_password" name="password"
                            placeholder="Leave blank to keep current"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Confirm Password
                        </label>
                        <input type="password" name="password_confirmation" placeholder="Confirm password"
                            class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <!-- Footer Buttons -->
                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 rounded-lg border text-gray-700 hover:bg-gray-100 transition">
                        Cancel
                    </button>

                    <button type="submit"
                        class="px-5 py-2 rounded-lg bg-indigo-600 text-white font-medium hover:bg-indigo-700 transition">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>


@endsection

@section('scripts')
    <script>
       function viewUser(id) {
    fetch(`/admin/users/${id}`)
        .then(response => response.json())
        .then(user => {
            document.getElementById('modalTitle').textContent = `Profile: ${user.name}`;
            document.getElementById('userDetails').innerHTML = `
            <div class="space-y-6">
                <!-- Profile Header -->
                <div class="flex items-center">
                    <div class="w-20 h-20 rounded-full ${user.profile ? 'bg-cover bg-center' : 'bg-indigo-100'} flex items-center justify-center"
                         style="${user.profile ? "background-image: url('" + (user.profile.startsWith('http') ? user.profile : '/storage/' + user.profile) + "')" : ''}">
                        ${!user.profile ? '<i class="fas fa-user text-indigo-600 text-2xl"></i>' : ''}
                    </div>
                    <div class="ml-6">
                        <h2 class="text-2xl font-bold text-gray-800">${user.name}</h2>
                        <p class="text-gray-600">${user.email}</p>
                        <div class="flex items-center mt-2 space-x-3">
                            <span class="px-3 py-1 rounded-full text-sm font-medium ${user.role == 1 ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800'}">
                                <i class="fas ${user.role == 1 ? 'fa-user-shield' : 'fa-user-graduate'} mr-1"></i>
                                ${user.role == 1 ? 'Administrator' : 'Student'}
                            </span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium ${user.email_verified_at ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">
                                <i class="fas ${user.email_verified_at ? 'fa-check-circle' : 'fa-clock'} mr-1"></i>
                                ${user.email_verified_at ? 'Verified' : 'Pending Verification'}
                            </span>
                        </div>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-gray-50 p-4 rounded-lg text-center">
                        <p class="text-2xl font-bold text-gray-800">${user.total_bookings_count || 0}</p>
                        <p class="text-sm text-gray-600">Total Bookings</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg text-center">
                        <p class="text-2xl font-bold text-gray-800">${user.active_bookings_count || 0}</p>
                        <p class="text-sm text-gray-600">Active Bookings</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg text-center">
                        <p class="text-2xl font-bold text-gray-800">${user.feedbacks_count || 0}</p>
                        <p class="text-sm text-gray-600">Feedbacks</p>
                    </div>
                </div>
                
                <!-- Details -->
                <div>
                    <h4 class="text-sm font-medium text-gray-500 mb-3">ACCOUNT DETAILS</h4>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Member Since</span>
                            <span class="font-medium">${new Date(user.created_at).toLocaleDateString()}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Email Verified</span>
                            <span class="font-medium">${user.email_verified_at ? new Date(user.email_verified_at).toLocaleDateString() : 'Not Verified'}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
            document.getElementById('userModal').classList.remove('hidden');
        });
}

        function openCreateModal() {
            document.getElementById('editModalTitle').textContent = 'Add New User';
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('passwordFields').style.display = 'block';
            document.getElementById('edit_password').required = true;

            // Set to create route
            document.getElementById('userForm').action = `/admin/users`;
            document.getElementById('userForm').method = 'POST';

            // Remove any existing _method input
            const existingMethod = document.querySelector('input[name="_method"]');
            if (existingMethod) {
                existingMethod.remove();
            }

            // Add CSRF token if not exists
            let csrfToken = document.querySelector('input[name="_token"]');
            if (!csrfToken) {
                csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';
                document.getElementById('userForm').prepend(csrfToken);
            }

            document.getElementById('editUserModal').classList.remove('hidden');
        }

        function editUser(id) {
            fetch(`/admin/users/${id}`)
                .then(response => response.json())
                .then(user => {
                    console.log('Editing user:', user);

                    document.getElementById('editModalTitle').textContent = 'Edit User';
                    document.getElementById('userId').value = user.id;
                    document.getElementById('edit_name').value = user.name;
                    document.getElementById('edit_email').value = user.email;
                    document.getElementById('edit_role').value = user.role;
                    document.getElementById('passwordFields').style.display = 'none';
                    document.getElementById('edit_password').required = false;

                    // Set the form action to the correct update route with user ID
                    document.getElementById('userForm').action = `/admin/users/${user.id}`;
                    document.getElementById('userForm').method = 'POST';

                    // Remove existing _method hidden input if any
                    const existingMethod = document.querySelector('input[name="_method"]');
                    if (existingMethod) {
                        existingMethod.remove();
                    }

                    // Add method spoofing for PUT request
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    document.getElementById('userForm').appendChild(methodInput);

                    // Add CSRF token if not exists
                    let csrfToken = document.querySelector('input[name="_token"]');
                    if (!csrfToken) {
                        csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        document.getElementById('userForm').prepend(csrfToken);
                    }

                    document.getElementById('editUserModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error fetching user:', error);
                    alert('Failed to load user data');
                });
        }


        function verifyUser(userId) {
            fetch(`/admin/users/${userId}/verify`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}', // Important for Laravel
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User verified!');
                        location.reload(); // reload to reflect change
                    }
                })
                .catch(err => console.error(err));
        }

        function confirmDelete(type, id) {
            if (confirm(`Are you sure you want to delete this ${type}? This action cannot be undone.`)) {
                fetch(`/admin/users/${id}`, {
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
                            alert('Failed to delete user');
                        }
                    });
            }
        }


        function searchUsers(query) {
            const url = new URL(window.location.href);
            if (query && query.trim() !== '') {
                url.searchParams.set('search', query.trim());
            } else {
                url.searchParams.delete('search');
            }
            // Reset to first page
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        function filterByRole(role) {
            const url = new URL(window.location.href);
            if (role) {
                url.searchParams.set('role', role);
            } else {
                url.searchParams.delete('role');
            }
            // Reset to first page
            url.searchParams.delete('page');
            window.location.href = url.toString();
        }

        function closeModal() {
            document.getElementById('userModal').classList.add('hidden');
        }

        function closeEditModal() {
            document.getElementById('editUserModal').classList.add('hidden');
        }

        // Handle form submission
        document.getElementById('userForm')?.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const url = this.action;

            // Determine method from _method input or default to POST
            let method = 'POST';
            const methodInput = this.querySelector('input[name="_method"]');
            if (methodInput && methodInput.value) {
                method = methodInput.value;
            }

            console.log('Submitting form to:', url);
            console.log('Method:', method);

            // Always use POST for FormData, Laravel will handle method spoofing via _method
            fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success || data.message) {
                        location.reload();
                    } else {
                        alert(data.message || 'Failed to save user');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred: ' + error.message);
                });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeModal();
                closeEditModal();
            }
        });
    </script>
@endsection
