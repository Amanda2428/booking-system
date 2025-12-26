@extends('layouts.admin')

@section('title', 'Room Categories')
@section('subtitle', 'Manage room categories and types')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="text-gray-500">Categories</li>
@endsection

@section('content')
    <!-- Header with Stats -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-800">Room Categories</h2>
            <p class="text-gray-600">Organize rooms into categories</p>
        </div>
        <div class="flex items-center space-x-3">
            <div class="text-sm text-gray-600">
                <span class="font-medium">{{ $categories->total() }}</span> categories
            </div>
            <div class="relative">
                <input type="text" 
                       placeholder="Search roomstype..." 
                       value="{{ request('search') }}"
                       onkeyup="searchRoomsType(this.value)"
                       class="pl-10 pr-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 w-full md:w-64">
                <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
            </div>
            <button onclick="openCreateModal()"
                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                <i class="fas fa-plus mr-2"></i> Add Category
            </button>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($categories as $category)
            <div class="bg-white rounded-lg shadow-sm border hover:shadow-md transition-shadow">
                <div class="p-6">
                    <!-- Category Header -->
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">{{ $category->name }}</h3>
                            <div class="flex items-center mt-2">
                                <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center">
                                    <i class="fas fa-tag text-indigo-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-500">Rooms in category</p>
                                    <p class="text-lg font-bold text-gray-800">{{ $category->rooms_count ?? 0 }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="editCategory({{ $category->id }})"
                                class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-lg">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="confirmDelete('category', {{ $category->id }})"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Description -->
                    @if ($category->description)
                        <div class="mb-4">
                            <p class="text-sm text-gray-500 mb-2">Description</p>
                            <p class="text-gray-700 line-clamp-2">{{ $category->description }}</p>
                        </div>
                    @endif

                    <!-- Stats -->
                    <div class="grid grid-cols-2 gap-4 pt-4 border-t">
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-800">{{ $category->available_rooms ?? 0 }}</p>
                            <p class="text-xs text-gray-500">Available</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl font-bold text-gray-800">{{ $category->avg_capacity ?? 0 }}</p>
                            <p class="text-xs text-gray-500">Avg Capacity</p>
                        </div>
                    </div>

                    <!-- View Rooms Button -->
                    <div class="mt-4">
                        <button onclick="viewCategoryRooms({{ $category->id }})"
                            class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center justify-center">
                            <i class="fas fa-door-open mr-2"></i> View Rooms
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="md:col-span-3">
                <div class="bg-white rounded-lg shadow-sm border p-12 text-center">
                    <i class="fas fa-tags text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-700 mb-2">No categories found</h3>
                    <p class="text-gray-500 mb-6">Create categories to organize your rooms</p>
                    <button onclick="openCreateModal()"
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i> Create Category
                    </button>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if ($categories->hasPages())
        <div class="mt-6">
            {{ $categories->links() }}
        </div>
    @endif

    <!-- Create/Edit Modal -->
    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl w-full max-w-md">
                <div class="p-6 border-b flex justify-between items-center">
                    <h3 class="text-xl font-semibold text-gray-800" id="modalTitle">Add New Category</h3>
                    <button onclick="closeModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="categoryForm" method="POST">
                    @csrf
                    <div class="p-6 space-y-4">
                        <input type="hidden" name="id" id="categoryId">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Category Name
                                *</label>
                            <input type="text" id="name" name="name" required
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                            <textarea id="description" name="description" rows="3"
                                class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                        </div>
                    </div>
                    <div class="p-6 border-t flex justify-end space-x-3">
                        <button type="button" onclick="closeModal()"
                            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                            Save Category
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Rooms Modal -->
    <div id="roomsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-xl w-full max-w-2xl max-h-[90vh] flex flex-col">

                <!-- Header -->
                <div class="p-6 border-b flex justify-between items-center flex-shrink-0">
                    <h3 class="text-xl font-semibold text-gray-800" id="roomsModalTitle">
                        Category Rooms
                    </h3>
                    <button onclick="closeRoomsModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Scrollable Content -->
                <div class="p-6 overflow-y-auto flex-1" id="roomsList">
                    <!-- Loaded via JS -->
                </div>

                <!-- Footer -->
                <div class="p-6 border-t flex-shrink-0">
                    <button onclick="closeRoomsModal()"
                        class="w-full px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                        Close
                    </button>
                </div>

            </div>
        </div>
    </div>


@endsection

@section('scripts')
    <script>
        /* =========================
       OPEN CREATE MODAL
    ========================= */
        function openCreateModal() {
            const form = document.getElementById('categoryForm');

            document.getElementById('modalTitle').textContent = 'Add New Category';

            // Reset form
            form.reset();

            // Remove PUT method if exists
            const oldMethod = form.querySelector('input[name="_method"]');
            if (oldMethod) oldMethod.remove();

            document.getElementById('categoryId').value = '';
            form.action = '/admin/categories';
            form.method = 'POST';

            document.getElementById('categoryModal').classList.remove('hidden');
        }

        /* =========================
           OPEN EDIT MODAL
        ========================= */
        function editCategory(id) {
            fetch(`/admin/categories/${id}`)
                .then(response => response.json())
                .then(category => {
                    const form = document.getElementById('categoryForm');

                    document.getElementById('modalTitle').textContent = 'Edit Category';

                    // Reset form
                    form.reset();

                    // Remove old _method
                    const oldMethod = form.querySelector('input[name="_method"]');
                    if (oldMethod) oldMethod.remove();

                    // Fill values
                    document.getElementById('categoryId').value = category.id;
                    document.getElementById('name').value = category.name;
                    document.getElementById('description').value = category.description ?? '';

                    // Set action
                    form.action = `/admin/categories/${id}`;
                    form.method = 'POST';

                    // Add PUT method
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'PUT';
                    form.appendChild(methodInput);

                    document.getElementById('categoryModal').classList.remove('hidden');
                })
                .catch(() => {
                    alert('Failed to load category data');
                });
        }

        /* =========================
           VIEW CATEGORY ROOMS
        ========================= */

   function viewCategoryRooms(id) { fetch(`/admin/categories/${id}`)
                .then(response => response.json())
                .then(category => {
                    document.getElementById('roomsModalTitle').textContent = `Rooms in ${category.name}`;

                    document.getElementById('roomsList').innerHTML = `
                <div class="space-y-4">
                    ${category.rooms && category.rooms.length > 0 ? category.rooms.map(room => `
                        <div class="p-4 border rounded-lg hover:bg-gray-50">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-gray-800">${room.name}</h4>
                                    <div class="flex items-center mt-1">
                                        <i class="fas fa-map-marker-alt text-gray-400 text-sm mr-2"></i>
                                        <span class="text-sm text-gray-600">${room.location}</span>
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium ${room.availability_status === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                    ${room.availability_status.charAt(0).toUpperCase() + room.availability_status.slice(1)}
                                </span>
                            </div>
                            <div class="mt-3 flex items-center justify-between text-sm text-gray-500">
                                <div class="flex items-center">
                                    <i class="fas fa-users mr-1"></i>
                                    <span>${room.capacity} capacity</span>
                                </div>
                                <a href="/admin/rooms/${room.id}/edit" class="text-indigo-600 hover:text-indigo-800"> Edit Room </a>
                            </div>
                        </div>`).join('') : `
                        <div class="text-center py-8">
                            <i class="fas fa-door-open text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No rooms in this category yet</p>
                            <a href="/admin/rooms/create" class="text-indigo-600 hover:text-indigo-800 mt-2 inline-block"> Add a room → </a>
                        </div>`}
                </div>`;

                    document.getElementById('roomsModal').classList.remove('hidden');
                });
        }
        /* =========================
           DELETE CATEGORY
        ========================= */
        function confirmDelete(type, id) {
            if (!confirm(`Delete this ${type}?`)) return;

            fetch(`/admin/categories/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(() => location.reload())
                .catch(() => alert('Delete failed'));
        }

        /* =========================
           CLOSE MODALS
        ========================= */
        function closeModal() {
            document.getElementById('categoryModal').classList.add('hidden');
        }

        function closeRoomsModal() {
            document.getElementById('roomsModal').classList.add('hidden');
        }

        /* =========================
           SUBMIT FORM (CREATE + EDIT)
        ========================= */
        document.getElementById('categoryForm')?.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const url = this.action;

            fetch(url, {
                    method: 'POST', 
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(async response => {
                    const data = await response.json();
                    if (!response.ok) throw data;
                    return data;
                })
                .then(() => {
                    location.reload();
                })
                .catch(error => {
                    if (error.errors?.name) {
                        alert(error.errors.name[0]);
                    } else {
                        alert('Failed to save category');
                    }
                });
        });

        /* =========================
           ESC KEY CLOSE
        ========================= */
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeModal();
                closeRoomsModal();
            }
        });

        function searchRoomsType(query) {
    const url = new URL(window.location.href);
    if (query) {
        url.searchParams.set('search', query);
    } else {
        url.searchParams.delete('search');
    }
    window.location.href = url.toString();
}
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
