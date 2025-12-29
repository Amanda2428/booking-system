@extends('layouts.admin')

@section('title', 'Create Booking')
@section('subtitle', 'Create a new booking for a user')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('admin.bookings') }}">Bookings</a></li>
    <li class="text-gray-500">Create Booking</li>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Display errors if any -->
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-600 mr-3"></i>
                    <div>
                        <h4 class="font-medium text-red-800">Please fix the following errors:</h4>
                        <ul class="mt-2 text-sm text-red-700">
                            @foreach ($errors->all() as $error)
                                @if(is_array($error))
                                    {{-- Skip arrays, they'll be handled separately --}}
                                @else
                                    <li class="flex items-center mt-1">
                                        <i class="fas fa-chevron-right text-xs mr-2"></i>
                                        {{ $error }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                        
                        {{-- Handle conflicting bookings separately --}}
                        @php
                            $conflictingBookings = [];
                            foreach ($errors->get('conflicts') as $conflictList) {
                                if (is_array($conflictList)) {
                                    foreach ($conflictList as $conflict) {
                                        if (is_array($conflict)) {
                                            $conflictingBookings = array_merge($conflictingBookings, $conflict);
                                        } else {
                                            $conflictingBookings[] = $conflict;
                                        }
                                    }
                                }
                            }
                        @endphp
                        
                        @if(count($conflictingBookings) > 0)
                            <div class="mt-4">
                                <h5 class="font-medium text-red-800 mb-2">Conflicting Approved Bookings:</h5>
                                @foreach ($conflictingBookings as $conflict)
                                    @if(is_object($conflict) && method_exists($conflict, 'getAttributes'))
                                        <div class="p-3 mb-2 bg-red-100 border border-red-300 rounded">
                                            <div class="flex justify-between items-start">
                                                <div>
                                                    <div class="font-medium text-red-800">Booking #{{ $conflict->id }}</div>
                                                    <div class="text-sm text-red-700">
                                                        User: {{ $conflict->user->name ?? 'Unknown User' }}
                                                    </div>
                                                    <div class="text-sm text-red-700">
                                                        Time: {{ \Carbon\Carbon::parse($conflict->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($conflict->end_time)->format('h:i A') }}
                                                    </div>
                                                </div>
                                                <span class="px-2 py-1 text-xs font-medium bg-green-200 text-green-800 rounded">
                                                    Approved
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Create New Booking</h2>
                <p class="text-gray-600 mt-1">Fill in the details to create a booking for a user</p>
            </div>
            
            <form action="{{ route('admin.bookings.store') }}" method="POST" class="p-6" id="bookingForm">
                @csrf
                
                <div class="space-y-6">
                    <!-- User Selection -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Select User</h3>
                        <div class="relative">
                            <select id="user_id" name="user_id" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $errors->has('user_id') ? 'border-red-500' : '' }}">
                                <option value="">Select a User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }}) - {{ $user->role == 1 ? 'Admin' : 'Student' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Room Selection -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Select Room</h3>
                        <div class="relative">
                            <select id="room_id" name="room_id" required
                                    class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $errors->has('room_id') ? 'border-red-500' : '' }}">
                                <option value="">Select a Room</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}" {{ old('room_id') == $room->id ? 'selected' : '' }}>
                                        {{ $room->name }} ({{ $room->location }}) - Capacity: {{ $room->capacity }} - 
                                        {{ $room->category->name ?? 'No Category' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('room_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mt-4 p-4 bg-gray-50 rounded-lg hidden" id="roomDetails">
                            <!-- Room details will be shown here -->
                        </div>
                    </div>
                    
                    <!-- Booking Details -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Booking Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date *
                                </label>
                                <input type="date" 
                                       id="date" 
                                       name="date" 
                                       value="{{ old('date', date('Y-m-d')) }}"
                                       required
                                       min="{{ date('Y-m-d') }}"
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $errors->has('date') ? 'border-red-500' : '' }}">
                                @error('date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    Start Time *
                                </label>
                                <select id="start_time" name="start_time" required
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $errors->has('start_time') ? 'border-red-500' : '' }}">
                                    <option value="">Select Start Time</option>
                                    @for($hour = 8; $hour <= 20; $hour++)
                                        @php
                                            $time = sprintf('%02d:00', $hour);
                                            $display = date('h:i A', strtotime($time));
                                        @endphp
                                        <option value="{{ $time }}" {{ old('start_time') == $time ? 'selected' : '' }}>
                                            {{ $display }}
                                        </option>
                                    @endfor
                                </select>
                                @error('start_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                    End Time *
                                </label>
                                <select id="end_time" name="end_time" required
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $errors->has('end_time') ? 'border-red-500' : '' }}">
                                    <option value="">Select End Time</option>
                                    @for($hour = 9; $hour <= 21; $hour++)
                                        @php
                                            $time = sprintf('%02d:00', $hour);
                                            $display = date('h:i A', strtotime($time));
                                        @endphp
                                        <option value="{{ $time }}" {{ old('end_time') == $time ? 'selected' : '' }}>
                                            {{ $display }}
                                        </option>
                                    @endfor
                                </select>
                                @error('end_time')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-4 p-4 rounded-lg hidden" id="timeValidation">
                            <!-- Time validation messages -->
                        </div>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Booking Status *
                        </label>
                        <div class="flex flex-wrap gap-4">
                            @foreach(['pending', 'approved', 'rejected'] as $status)
                                <label class="inline-flex items-center">
                                    <input type="radio" 
                                           name="status" 
                                           value="{{ $status }}"
                                           {{ old('status', 'pending') == $status ? 'checked' : '' }}
                                           class="text-indigo-600 focus:ring-indigo-500"
                                           id="status_{{ $status }}">
                                    <span class="ml-2 capitalize">{{ $status }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div class="mt-2 text-sm text-gray-600">
                            <p id="statusHelp_pending" class="hidden">
                                <i class="fas fa-info-circle mr-1"></i> Multiple pending bookings can exist for the same time.
                            </p>
                            <p id="statusHelp_approved" class="hidden">
                                <i class="fas fa-check-circle mr-1 text-green-600"></i> Only one approved booking per time slot. Will check for conflicts.
                            </p>
                            <p id="statusHelp_rejected" class="hidden">
                                <i class="fas fa-times-circle mr-1 text-red-600"></i> Booking will be rejected immediately.
                            </p>
                        </div>
                        @error('status')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Purpose (Optional) -->
                    <div>
                        <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">
                            Purpose (Optional)
                        </label>
                        <textarea id="purpose" 
                                  name="purpose" 
                                  rows="3"
                                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $errors->has('purpose') ? 'border-red-500' : '' }}"
                                  placeholder="Enter the purpose of this booking...">{{ old('purpose') }}</textarea>
                        @error('purpose')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <!-- Submit -->
                    <div class="pt-6 border-t flex justify-end space-x-3">
                        <a href="{{ route('admin.bookings') }}" 
                           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                id="submitBtn"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                            <i class="fas fa-calendar-plus mr-2"></i> Create Booking
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Room Availability Checker -->
        <div class="mt-6 bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b">
                <h3 class="text-lg font-semibold text-gray-800">Check Room Availability</h3>
                <p class="text-gray-600 mt-1">Check if the room is available for your selected time</p>
            </div>
            <div class="p-6">
                <div class="flex items-center gap-4 mb-4">
                    <button type="button" onclick="checkAvailability()"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
                        <i class="fas fa-search mr-2"></i> Check Availability
                    </button>
                    <div class="text-sm text-gray-600">
                        <i class="fas fa-info-circle mr-1"></i> Only checks against <strong>approved</strong> bookings
                    </div>
                </div>
                <div class="mt-4 p-4 rounded-lg hidden" id="availabilityResult">
                    <!-- Availability result will be shown here -->
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Show room details when room is selected
        document.getElementById('room_id').addEventListener('change', function() {
            const roomId = this.value;
            const roomDetails = document.getElementById('roomDetails');
            
            if (!roomId) {
                roomDetails.classList.add('hidden');
                return;
            }
            
            // Fetch room details
            fetch(`/admin/rooms/${roomId}`)
                .then(response => response.json())
                .then(room => {
                    roomDetails.innerHTML = `
                        <div class="flex items-start">
                            <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center mr-4">
                                <i class="fas fa-door-open text-blue-600"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800">${room.name}</h4>
                                <div class="text-sm text-gray-600 mt-1">
                                    <div class="flex items-center">
                                        <i class="fas fa-map-marker-alt mr-2"></i>
                                        ${room.location}
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <i class="fas fa-users mr-2"></i>
                                        Capacity: ${room.capacity} people
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <i class="fas fa-tag mr-2"></i>
                                        ${room.category ? room.category.name : 'No Category'}
                                    </div>
                                    <div class="flex items-center mt-1">
                                        <i class="fas fa-circle mr-2 text-${room.availability_status === 'available' ? 'green' : 'red'}-500"></i>
                                        Status: ${room.availability_status.charAt(0).toUpperCase() + room.availability_status.slice(1)}
                                    </div>
                                </div>
                                ${room.description ? `
                                    <p class="text-sm text-gray-700 mt-2">${room.description}</p>
                                ` : ''}
                            </div>
                        </div>
                    `;
                    roomDetails.classList.remove('hidden');
                })
                .catch(err => {
                    roomDetails.innerHTML = `
                        <div class="text-red-600">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            Unable to load room details
                        </div>
                    `;
                    roomDetails.classList.remove('hidden');
                });
        });
        
        // Show status help text
        const statusRadios = document.querySelectorAll('input[name="status"]');
        statusRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                // Hide all help texts
                document.querySelectorAll('[id^="statusHelp_"]').forEach(el => {
                    el.classList.add('hidden');
                });
                
                // Show selected status help
                const status = this.value;
                const helpText = document.getElementById(`statusHelp_${status}`);
                if (helpText) {
                    helpText.classList.remove('hidden');
                }
                
                // If selecting "approved", check for conflicts immediately
                if (status === 'approved') {
                    setTimeout(() => checkAvailability(), 100);
                } else {
                    // Enable submit button for non-approved statuses
                    const submitBtn = document.getElementById('submitBtn');
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    
                    // Clear any previous availability warnings
                    const resultDiv = document.getElementById('availabilityResult');
                    resultDiv.classList.add('hidden');
                }
            });
        });
        
        // Initialize status help text
        const initialStatus = document.querySelector('input[name="status"]:checked');
        if (initialStatus) {
            const helpText = document.getElementById(`statusHelp_${initialStatus.value}`);
            if (helpText) {
                helpText.classList.remove('hidden');
            }
        }
        
        // Validate time selection
        function validateTime() {
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;
            const validationDiv = document.getElementById('timeValidation');
            
            if (!startTime || !endTime) {
                validationDiv.classList.add('hidden');
                return { valid: false, message: '' };
            }
            
            const start = new Date(`2000-01-01T${startTime}`);
            const end = new Date(`2000-01-01T${endTime}`);
            const duration = (end - start) / (1000 * 60 * 60); // Hours
            
            let message = '';
            let colorClass = '';
            let valid = true;
            
            if (duration <= 0) {
                message = 'End time must be after start time';
                colorClass = 'bg-red-50 text-red-700 border-red-200';
                valid = false;
            } else if (duration > 4) {
                message = `Booking duration is ${duration.toFixed(1)} hours (maximum 4 hours allowed)`;
                colorClass = 'bg-red-50 text-red-700 border-red-200';
                valid = false;
            } else if (start.getHours() < 8 || end.getHours() > 21 || (end.getHours() == 21 && end.getMinutes() > 0)) {
                message = 'Bookings are only allowed between 8:00 AM and 9:00 PM';
                colorClass = 'bg-red-50 text-red-700 border-red-200';
                valid = false;
            } else {
                message = `Booking duration: ${duration.toFixed(1)} hours`;
                colorClass = 'bg-green-50 text-green-700 border-green-200';
            }
            
            validationDiv.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-clock mr-3"></i>
                    <span>${message}</span>
                </div>
            `;
            validationDiv.className = `p-4 rounded-lg border ${colorClass}`;
            validationDiv.classList.remove('hidden');
            
            return { valid: valid, duration: duration, message: message };
        }
        
        // Add event listeners for time validation
        document.getElementById('start_time').addEventListener('change', validateTime);
        document.getElementById('end_time').addEventListener('change', validateTime);
        
        // Check room availability
        function checkAvailability() {
            const roomId = document.getElementById('room_id').value;
            const date = document.getElementById('date').value;
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;
            const resultDiv = document.getElementById('availabilityResult');
            const submitBtn = document.getElementById('submitBtn');
            const status = document.querySelector('input[name="status"]:checked')?.value;
            
            // Only check for approved status
            if (status !== 'approved') {
                resultDiv.innerHTML = `
                    <div class="text-blue-700 bg-blue-50 border border-blue-200 p-4 rounded-lg">
                        <i class="fas fa-info-circle mr-2"></i>
                        Availability check is only needed for "approved" status bookings.
                    </div>
                `;
                resultDiv.classList.remove('hidden');
                return;
            }
            
            if (!roomId || !date || !startTime || !endTime) {
                resultDiv.innerHTML = `
                    <div class="text-yellow-700 bg-yellow-50 border border-yellow-200 p-4 rounded-lg">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        Please select room, date, and time first
                    </div>
                `;
                resultDiv.classList.remove('hidden');
                return;
            }
            
            // Validate time first
            const timeValidation = validateTime();
            if (!timeValidation.valid) {
                resultDiv.innerHTML = `
                    <div class="text-red-700 bg-red-50 border border-red-200 p-4 rounded-lg">
                        <i class="fas fa-times-circle mr-2"></i>
                        Please fix time selection errors first
                    </div>
                `;
                resultDiv.classList.remove('hidden');
                return;
            }
            
            resultDiv.innerHTML = `
                <div class="text-gray-600 bg-gray-50 border border-gray-200 p-4 rounded-lg">
                    <i class="fas fa-spinner fa-spin mr-2"></i>
                    Checking availability against approved bookings...
                </div>
            `;
            resultDiv.classList.remove('hidden');
            
            // Check for conflicts with approved bookings
            fetch(`/admin/bookings/check-availability`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    room_id: roomId,
                    date: date,
                    start_time: startTime,
                    end_time: endTime
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.available) {
                    resultDiv.innerHTML = `
                        <div class="text-green-700 bg-green-50 border border-green-200 p-4 rounded-lg">
                            <i class="fas fa-check-circle mr-2"></i>
                            Room is available for the selected time!
                        </div>
                        <p class="mt-2 text-sm text-gray-600">No approved bookings conflict with this time slot.</p>
                    `;
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else {
                    resultDiv.innerHTML = `
                        <div class="text-red-700 bg-red-50 border border-red-200 p-4 rounded-lg">
                            <i class="fas fa-times-circle mr-2"></i>
                            Room is not available for the selected time
                        </div>
                        <p class="mt-2 text-sm text-gray-700">${data.message || 'There is a conflict with an approved booking.'}</p>
                        ${data.conflicting_bookings && data.conflicting_bookings.length > 0 ? `
                            <div class="mt-3">
                                <h5 class="font-medium text-gray-700 mb-2">Conflicting Approved Bookings:</h5>
                                ${data.conflicting_bookings.map(booking => `
                                    <div class="p-3 mb-2 bg-red-50 border border-red-200 rounded">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <div class="font-medium text-red-800">Booking #${booking.id}</div>
                                                <div class="text-sm text-red-700 mt-1">
                                                    User: ${booking.user ? booking.user.name : 'Unknown User'}
                                                </div>
                                                <div class="text-sm text-red-700">
                                                    Time: ${formatTime(booking.start_time)} - ${formatTime(booking.end_time)}
                                                </div>
                                                <div class="text-sm text-red-700">
                                                    Status: <span class="font-medium">${booking.status}</span>
                                                </div>
                                            </div>
                                            <span class="px-2 py-1 text-xs font-medium bg-green-200 text-green-800 rounded">
                                                Approved
                                            </span>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        ` : ''}
                    `;
                    
                    // Disable submit button if there are conflicts for approved status
                    if (status === 'approved') {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                }
            })
            .catch(err => {
                resultDiv.innerHTML = `
                    <div class="text-red-700 bg-red-50 border border-red-200 p-4 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        Error checking availability: ${err.message}
                    </div>
                `;
                console.error('Error checking availability:', err);
            });
        }
        
        function formatTime(time) {
            if (!time) return 'N/A';
            const [hours, minutes] = time.split(':');
            const date = new Date();
            date.setHours(parseInt(hours), parseInt(minutes || 0));
            return date.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit', 
                hour12: true 
            });
        }
        
        // Form validation before submission
        document.getElementById('bookingForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const roomId = document.getElementById('room_id').value;
            const date = document.getElementById('date').value;
            const startTime = document.getElementById('start_time').value;
            const endTime = document.getElementById('end_time').value;
            const status = document.querySelector('input[name="status"]:checked')?.value;
            const userId = document.getElementById('user_id').value;
            
            // Basic validation
            if (!roomId || !date || !startTime || !endTime || !status || !userId) {
                alert('Please fill in all required fields');
                return;
            }
            
            // Time validation
            const timeValidation = validateTime();
            if (!timeValidation.valid) {
                alert(timeValidation.message);
                return;
            }
            
            // If status is "approved", double-check availability
            if (status === 'approved') {
                // Perform final availability check
                fetch(`/admin/bookings/check-availability`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        room_id: roomId,
                        date: date,
                        start_time: startTime,
                        end_time: endTime
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.available) {
                        // If available, submit the form
                        if (confirm('Create approved booking for this time?')) {
                            document.getElementById('bookingForm').submit();
                        }
                    } else {
                        alert('Cannot create approved booking: Room is already booked for this time. Please choose a different time or set status to "pending".');
                        
                        // Show conflicts in the availability result div
                        const resultDiv = document.getElementById('availabilityResult');
                        resultDiv.innerHTML = `
                            <div class="text-red-700 bg-red-50 border border-red-200 p-4 rounded-lg">
                                <i class="fas fa-times-circle mr-2"></i>
                                Cannot create approved booking due to conflicts
                            </div>
                            <p class="mt-2 text-sm text-gray-700">${data.message || 'There is a conflict with an approved booking.'}</p>
                        `;
                        resultDiv.classList.remove('hidden');
                    }
                })
                .catch(err => {
                    alert('Error checking availability. Please try again.');
                    console.error('Error:', err);
                });
            } else {
                // For pending/rejected bookings, just ask for confirmation
                const confirmSubmit = confirm(`Create this booking with status: ${status}?`);
                if (confirmSubmit) {
                    document.getElementById('bookingForm').submit();
                }
            }
        });
        
        // Initial validation
        validateTime();
        
        // Auto-check availability when any field changes (for approved status)
        document.getElementById('room_id').addEventListener('change', function() {
            const status = document.querySelector('input[name="status"]:checked')?.value;
            if (status === 'approved') {
                setTimeout(() => checkAvailability(), 300);
            }
        });
        
        document.getElementById('date').addEventListener('change', function() {
            const status = document.querySelector('input[name="status"]:checked')?.value;
            if (status === 'approved') {
                setTimeout(() => checkAvailability(), 300);
            }
        });
        
        document.getElementById('start_time').addEventListener('change', function() {
            const status = document.querySelector('input[name="status"]:checked')?.value;
            if (status === 'approved') {
                setTimeout(() => checkAvailability(), 300);
            }
        });
        
        document.getElementById('end_time').addEventListener('change', function() {
            const status = document.querySelector('input[name="status"]:checked')?.value;
            if (status === 'approved') {
                setTimeout(() => checkAvailability(), 300);
            }
        });
    </script>
@endsection