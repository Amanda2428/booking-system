@extends('layouts.user')

@section('title', 'Book a Room')
@section('subtitle', 'Reserve a study room for your needs')

@section('breadcrumb')
    <li><a href="{{ route('user.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('user.bookings') }}">My Bookings</a></li>
    <li class="text-gray-500">New Booking</li>
@endsection

@section('header-actions')
    <a href="{{ route('user.rooms') }}"
        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 flex items-center">
        <i class="fas fa-door-open mr-2"></i> Browse Rooms
    </a>
@endsection

@section('content')
    <div class="max-w-6xl mx-auto">
        <!-- Steps Indicator -->
        <div class="mb-10">
            <!-- Steps -->
            <div class="flex items-center justify-center">
                <div class="flex items-center space-x-4">

                    <!-- Step 1 -->
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow-lg">
                            <i class="fas fa-calendar text-lg"></i>
                        </div>
                        <div class="h-1 w-20 bg-indigo-600 rounded-full"></div>
                    </div>

                    <!-- Step 2 -->
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow-lg">
                            <i class="fas fa-door-open text-lg"></i>
                        </div>
                        <div class="h-1 w-20 bg-indigo-600 rounded-full"></div>
                    </div>

                    <!-- Step 3 -->
                    <div class="flex items-center">
                        <div
                            class="w-12 h-12 rounded-full bg-indigo-600 text-white flex items-center justify-center shadow-lg">
                            <i class="fas fa-clock text-lg"></i>
                        </div>
                        <div class="h-1 w-20 bg-gray-300 rounded-full"></div>
                    </div>

                    <!-- Step 4 -->
                    <div class="w-12 h-12 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center">
                        <i class="fas fa-check text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Labels -->
            <div class="grid grid-cols-4 text-center mt-3 text-sm font-medium text-gray-600">
                <span>Select Date</span>
                <span>Choose Room</span>
                <span>Pick Time</span>
                <span>Confirm</span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Booking Form -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-semibold text-gray-800">New Booking Request</h3>
                        <p class="text-gray-600 text-sm mt-1">Fill in the details to book a study room</p>
                    </div>

                    <form action="{{ route('user.bookings.store') }}" method="POST" id="bookingForm" class="p-6">
                        @csrf

                        <div class="space-y-8">
                            <!-- Step 1: Date Selection -->
                            <div>
                                <h4 class="text-lg font-medium text-gray-800 mb-4">1. Select Date</h4>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    @for ($i = 0; $i < 7; $i++)
                                        @php
                                            $date = now()->addDays($i);
                                            $isToday = $i == 0;
                                            $isSelected = old('date') == $date->format('Y-m-d');
                                        @endphp
                                        <label class="relative">
                                            <input type="radio" name="date" value="{{ $date->format('Y-m-d') }}"
                                                {{ $isSelected ? 'checked' : ($isToday && !old('date') ? 'checked' : '') }}
                                                class="hidden peer" onchange="loadAvailableRooms()">
                                            <div
                                                class="p-4 border rounded-lg text-center cursor-pointer hover:border-indigo-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50">
                                                <div class="text-sm text-gray-500">{{ $date->format('D') }}</div>
                                                <div class="text-2xl font-bold text-gray-800">{{ $date->format('d') }}</div>
                                                <div class="text-sm text-gray-600">{{ $date->format('M') }}</div>
                                                @if ($isToday)
                                                    <div class="mt-2 text-xs text-green-600 font-medium">Today</div>
                                                @endif
                                            </div>
                                        </label>
                                    @endfor
                                </div>
                                @error('date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Step 2: Room Selection -->
                            <div>
                                <h4 class="text-lg font-medium text-gray-800 mb-4">2. Choose Room</h4>
                                <div id="roomsContainer">
                                    <!-- Rooms will be loaded here based on selected date -->
                                    <div class="text-center py-12">
                                        <i class="fas fa-door-open text-4xl text-gray-300 mb-4"></i>
                                        <p class="text-gray-600">Select a date first to see available rooms</p>
                                    </div>
                                </div>
                                @error('room_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Step 3: Time Selection -->
                            <div>
                                <h4 class="text-lg font-medium text-gray-800 mb-4">3. Select Time Slot</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">
                                            Start Time *
                                        </label>
                                        <select id="start_time" name="start_time" required
                                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <option value="">Select Start Time</option>
                                            @for ($hour = 8; $hour <= 20; $hour++)
                                                @php
                                                    $time = sprintf('%02d:00', $hour);
                                                    $display = date('h:i A', strtotime($time));
                                                @endphp
                                                <option value="{{ $time }}"
                                                    {{ old('start_time') == $time ? 'selected' : '' }}>
                                                    {{ $display }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('start_time')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">
                                            End Time *
                                        </label>
                                        <select id="end_time" name="end_time" required
                                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                            <option value="">Select End Time</option>
                                            @for ($hour = 9; $hour <= 21; $hour++)
                                                @php
                                                    $time = sprintf('%02d:00', $hour);
                                                    $display = date('h:i A', strtotime($time));
                                                @endphp
                                                <option value="{{ $time }}"
                                                    {{ old('end_time') == $time ? 'selected' : '' }}>
                                                    {{ $display }}
                                                </option>
                                            @endfor
                                        </select>
                                        @error('end_time')
                                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mt-4 p-4 bg-blue-50 rounded-lg hidden" id="timeValidation">
                                    <!-- Time validation messages -->
                                </div>
                            </div>

                            <!-- Step 4: Purpose -->
                            <div>
                                <h4 class="text-lg font-medium text-gray-800 mb-4">4. Additional Information</h4>
                                <div>
                                    <label for="purpose" class="block text-sm font-medium text-gray-700 mb-2">
                                        Purpose (Optional)
                                    </label>
                                    <textarea id="purpose" name="purpose" rows="3"
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                        placeholder="What will you be using this room for? (e.g., group study, project work, exam preparation...)">{{ old('purpose') }}</textarea>
                                    <p class="mt-2 text-sm text-gray-500">Letting us know your purpose helps us improve our
                                        services.</p>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="pt-8 border-t">
                                <div class="flex justify-between items-center">
                                    <a href="{{ route('user.bookings') }}"
                                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                                        Cancel
                                    </a>
                                    <button type="submit" id="submitBtn"
                                        class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center justify-center text-lg disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-indigo-600 transition-colors duration-200"
                                        disabled>
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        <span id="submitText">Submit Booking Request</span>
                                        <span id="submitStatus" class="ml-2 text-sm"></span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Booking Summary -->
            <div>
                <div class="bg-white rounded-xl shadow-sm border p-6 sticky top-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Booking Summary</h3>

                    <div class="space-y-6">
                        <!-- Selected Date -->
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Selected Date</p>
                            <p id="selectedDate" class="font-medium text-gray-800">Not selected</p>
                        </div>

                        <!-- Selected Room -->
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Selected Room</p>
                            <div id="selectedRoom" class="text-gray-800">
                                <div class="text-gray-400 italic">No room selected</div>
                            </div>
                        </div>

                        <!-- Selected Time -->
                        <div>
                            <p class="text-sm text-gray-500 mb-2">Time Slot</p>
                            <p id="selectedTime" class="font-medium text-gray-800">Not selected</p>
                            <p id="duration" class="text-sm text-gray-600 mt-1"></p>
                        </div>

                        <!-- Rules & Guidelines -->
                        <div class="pt-6 border-t">
                            <h4 class="font-medium text-gray-800 mb-3">Booking Guidelines</h4>
                            <ul class="space-y-2 text-sm text-gray-600">
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                                    <span>Maximum booking duration: 4 hours</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                                    <span>Operating hours: 8:00 AM - 9:00 PM</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                                    <span>Bookings require admin approval</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                                    <span>Cancellations must be made 2 hours in advance</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-check-circle text-green-500 mr-2 mt-0.5"></i>
                                    <span>Maximum 3 active bookings at a time</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Availability Check -->
                        <div class="pt-6 border-t">
                            <button onclick="checkAvailability()"
                                class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center">
                                <i class="fas fa-search mr-2"></i> Check Availability
                            </button>
                            <div class="mt-4 p-4 rounded-lg hidden" id="availabilityResult">
                                <!-- Availability result will be shown here -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    let selectedRoomId = null;
    let selectedDate = null;
    let isRoomAvailable = false;

    // Load available rooms based on selected date
    function loadAvailableRooms() {
        const dateInput = document.querySelector('input[name="date"]:checked');
        if (!dateInput) return;

        selectedDate = dateInput.value;

        // Update summary
        const date = new Date(selectedDate);
        document.getElementById('selectedDate').textContent =
            date.toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });

        // Show loading
        document.getElementById('roomsContainer').innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-4xl text-gray-300 mb-4"></i>
                <p class="text-gray-600">Loading available rooms...</p>
            </div>
        `;

        // Reset availability
        isRoomAvailable = false;
        updateSubmitButtonStatus();
        document.getElementById('availabilityResult').classList.add('hidden');

        // Get room_id from URL parameters
        const urlParams = new URLSearchParams(window.location.search);
        const urlRoomId = urlParams.get('room_id');

        // Fetch available rooms
        fetch(`/user/rooms/available?date=${selectedDate}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success && data.rooms && data.rooms.length > 0) {
                    let roomsHTML = '<div class="grid grid-cols-1 gap-4">';

                    data.rooms.forEach(room => {
                        const isUrlRoom = urlRoomId && parseInt(urlRoomId) === room.id;
                        const isOldRoom = {{ old('room_id', 0) }} == room.id;
                        
                        roomsHTML += `
                            <label class="relative">
                                <input type="radio" 
                                       name="room_id" 
                                       value="${room.id}"
                                       ${(isOldRoom || isUrlRoom) ? 'checked' : ''}
                                       class="hidden peer"
                                       onchange="handleRoomSelection(${room.id}, '${room.name.replace(/'/g, "\\'")}', '${room.location.replace(/'/g, "\\'")}', ${room.capacity})">
                                <div class="p-4 border rounded-lg cursor-pointer hover:border-indigo-300 peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-all duration-200">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h5 class="font-semibold text-gray-800">${room.name}</h5>
                                            <p class="text-sm text-gray-600">${room.location}</p>
                                            <div class="flex items-center mt-2">
                                                <i class="fas fa-users text-gray-400 text-sm mr-2"></i>
                                                <span class="text-sm text-gray-500">${room.capacity} people</span>
                                                <span class="mx-3 text-gray-300">•</span>
                                                <i class="fas fa-tag text-gray-400 text-sm mr-2"></i>
                                                <span class="text-sm text-gray-500">${room.category ? room.category.name : 'General'}</span>
                                            </div>
                                        </div>
                                        <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center">
                                            <i class="fas fa-door-open text-blue-600"></i>
                                        </div>
                                    </div>
                                    ${room.description ? `
                                        <p class="mt-3 text-sm text-gray-700">${room.description}</p>
                                        ` : ''}
                                </div>
                            </label>
                        `;
                    });

                    roomsHTML += '</div>';
                    document.getElementById('roomsContainer').innerHTML = roomsHTML;

                    // Auto-select room from URL parameter
                    if (urlRoomId) {
                        const room = data.rooms.find(r => r.id == urlRoomId);
                        if (room) {
                            const radioButton = document.querySelector(`input[name="room_id"][value="${room.id}"]`);
                            if (radioButton) {
                                radioButton.checked = true;
                                handleRoomSelection(room.id, room.name, room.location, room.capacity);
                            }
                        }
                    } else if ({{ old('room_id', 0) }}) {
                        const oldRoomId = {{ old('room_id', 0) }};
                        const room = data.rooms.find(r => r.id == oldRoomId);
                        if (room) {
                            handleRoomSelection(room.id, room.name, room.location, room.capacity);
                        }
                    }
                } else {
                    document.getElementById('roomsContainer').innerHTML = `
                        <div class="text-center py-12">
                            <i class="fas fa-door-closed text-4xl text-gray-300 mb-4"></i>
                            <h4 class="text-lg font-medium text-gray-700 mb-2">No rooms available</h4>
                            <p class="text-gray-600">No rooms are available for the selected date.</p>
                            <p class="text-sm text-gray-500 mt-2">Please try a different date or contact support.</p>
                        </div>
                    `;
                }
            })
            .catch(err => {
                console.error('Error:', err);
                document.getElementById('roomsContainer').innerHTML = `
                    <div class="text-center py-12">
                        <i class="fas fa-exclamation-circle text-4xl text-red-300 mb-4"></i>
                        <p class="text-red-600">Error loading rooms. Please try again.</p>
                        <p class="text-sm text-gray-500 mt-2">${err.message}</p>
                    </div>
                `;
            });
    }

    function handleRoomSelection(id, name, location, capacity) {
        selectedRoomId = id;
        isRoomAvailable = false;
        updateSubmitButtonStatus();
        document.getElementById('availabilityResult').classList.add('hidden');

        // Update summary
        document.getElementById('selectedRoom').innerHTML = `
            <div>
                <h5 class="font-semibold text-gray-800">${name}</h5>
                <p class="text-sm text-gray-600">${location}</p>
                <p class="text-sm text-gray-500 mt-1">
                    <i class="fas fa-users mr-1"></i> ${capacity} people capacity
                </p>
            </div>
        `;

        validateTime();
    }

    function validateTime() {
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const validationDiv = document.getElementById('timeValidation');

        if (!startTime || !endTime) {
            validationDiv.classList.add('hidden');
            updateTimeSummary(startTime, endTime);
            
            isRoomAvailable = false;
            updateSubmitButtonStatus();
            document.getElementById('availabilityResult').classList.add('hidden');
            
            return {
                valid: false,
                message: ''
            };
        }

        const start = new Date(`2000-01-01T${startTime}`);
        const end = new Date(`2000-01-01T${endTime}`);
        const duration = (end - start) / (1000 * 60 * 60);

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

        updateTimeSummary(startTime, endTime);

        if (isRoomAvailable && !valid) {
            isRoomAvailable = false;
            updateSubmitButtonStatus();
            document.getElementById('availabilityResult').classList.add('hidden');
        }

        return {
            valid: valid,
            duration: duration,
            message: message
        };
    }

    function updateTimeSummary(startTime, endTime) {
        const timeDiv = document.getElementById('selectedTime');
        const durationDiv = document.getElementById('duration');

        if (!startTime || !endTime) {
            timeDiv.textContent = 'Not selected';
            durationDiv.textContent = '';
            return;
        }

        timeDiv.textContent = `${formatTime(startTime)} - ${formatTime(endTime)}`;

        const start = new Date(`2000-01-01T${startTime}`);
        const end = new Date(`2000-01-01T${endTime}`);
        const duration = (end - start) / (1000 * 60 * 60);
        durationDiv.textContent = `Duration: ${duration.toFixed(1)} hours`;
    }

    function formatTime(time) {
        const [hours, minutes] = time.split(':');
        const date = new Date();
        date.setHours(parseInt(hours), parseInt(minutes || 0));
        return date.toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit',
            hour12: true
        });
    }

    function checkAvailability() {
        if (!selectedRoomId || !selectedDate) {
            showAlert('error', 
                '<div class="flex items-center mb-4">' +
                '<div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">' +
                '<i class="fas fa-exclamation-triangle text-red-600"></i>' +
                '</div>' +
                '<div>' +
                '<h4 class="font-semibold text-gray-800">Selection Required</h4>' +
                '<p class="text-sm text-gray-600">Please select a date and room first</p>' +
                '</div>' +
                '</div>'
            );
            return;
        }

        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const resultDiv = document.getElementById('availabilityResult');

        if (!startTime || !endTime) {
            showAlert('error',
                '<div class="flex items-center mb-4">' +
                '<div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">' +
                '<i class="fas fa-exclamation-triangle text-red-600"></i>' +
                '</div>' +
                '<div>' +
                '<h4 class="font-semibold text-gray-800">Time Selection Required</h4>' +
                '<p class="text-sm text-gray-600">Please select start and end times</p>' +
                '</div>' +
                '</div>'
            );
            return;
        }

        const timeValidation = validateTime();
        if (!timeValidation.valid) {
            showAlert('error',
                '<div class="flex items-center mb-4">' +
                '<div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">' +
                '<i class="fas fa-exclamation-triangle text-red-600"></i>' +
                '</div>' +
                '<div>' +
                '<h4 class="font-semibold text-gray-800">Invalid Time Selection</h4>' +
                '<p class="text-sm text-gray-600">' + timeValidation.message + '</p>' +
                '</div>' +
                '</div>'
            );
            return;
        }

        resultDiv.innerHTML = `
            <div class="flex items-center text-gray-600 bg-gray-50 border border-gray-200 p-4 rounded-lg">
                <i class="fas fa-spinner fa-spin mr-3"></i>
                <span>Checking room availability for selected time slot...</span>
            </div>
        `;
        resultDiv.classList.remove('hidden');
        
        updateSubmitButtonStatus();

        fetch(`/user/bookings/check-availability`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    room_id: selectedRoomId,
                    date: selectedDate,
                    start_time: startTime,
                    end_time: endTime
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.available) {
                    resultDiv.innerHTML = `
                        <div class="text-green-700 bg-green-50 border border-green-200 p-4 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-xl mr-3"></i>
                                <div>
                                    <h4 class="font-semibold">Room is Available!</h4>
                                    <p class="text-sm mt-1">The room is free for your selected time slot.</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm text-blue-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                You can now proceed to submit your booking request.
                            </p>
                        </div>
                    `;
                    isRoomAvailable = true;
                    updateSubmitButtonStatus();
                    
                    showAlert('success',
                        '<div class="flex items-center mb-4">' +
                        '<div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center mr-4">' +
                        '<i class="fas fa-check-circle text-green-600 text-xl"></i>' +
                        '</div>' +
                        '<div>' +
                        '<h4 class="text-lg font-semibold text-gray-800">Room Available!</h4>' +
                        '<p class="text-gray-600">Great news! The room is available for your selected time.</p>' +
                        '</div>' +
                        '</div>' +
                        '<div class="mt-4 p-3 bg-green-50 rounded-lg">' +
                        '<p class="text-sm text-green-700">' +
                        '<i class="fas fa-lightbulb mr-2"></i>' +
                        'You can now submit your booking request. Click the "Submit Booking Request" button below.' +
                        '</p>' +
                        '</div>'
                    );
                    
                } else {
                    let suggestionsHTML = '';
                    if (data.suggestions && data.suggestions.length > 0) {
                        suggestionsHTML = `
                            <div class="mt-4">
                                <h5 class="font-medium text-gray-700 mb-2">Suggested Alternatives:</h5>
                                <div class="space-y-2">
                                    ${data.suggestions.map(suggestion => `
                                        <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg">
                                            <div class="font-medium text-gray-800">${suggestion.time}</div>
                                            <div class="text-sm text-blue-700 mt-1">${suggestion.message}</div>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    }
                    
                    resultDiv.innerHTML = `
                        <div class="text-red-700 bg-red-50 border border-red-200 p-4 rounded-lg">
                            <div class="flex items-center">
                                <i class="fas fa-times-circle text-xl mr-3"></i>
                                <div>
                                    <h4 class="font-semibold">Room Not Available</h4>
                                    <p class="text-sm mt-1">${data.message || 'The room is already booked for your selected time.'}</p>
                                </div>
                            </div>
                        </div>
                        ${suggestionsHTML}
                        <div class="mt-3 p-3 bg-yellow-50 rounded-lg">
                            <p class="text-sm text-yellow-700">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                Please choose a different time or room and check availability again.
                            </p>
                        </div>
                    `;
                    
                    isRoomAvailable = false;
                    updateSubmitButtonStatus();
                    
                    let alertMessage = 
                        '<div class="flex items-center mb-4">' +
                        '<div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mr-4">' +
                        '<i class="fas fa-times-circle text-red-600 text-xl"></i>' +
                        '</div>' +
                        '<div>' +
                        '<h4 class="text-lg font-semibold text-gray-800">Cannot Book Room</h4>' +
                        '<p class="text-gray-600">The room is not available for your selected time.</p>' +
                        '</div>' +
                        '</div>' +
                        '<div class="mt-4 p-4 bg-red-50 rounded-lg">' +
                        '<p class="text-red-700">' +
                        '<i class="fas fa-info-circle mr-2"></i>' +
                        'This room is already booked during your chosen time slot. ' +
                        'Please choose a different time or select another room.' +
                        '</p>' +
                        '</div>';
                    
                    if (data.suggestions && data.suggestions.length > 0) {
                        alertMessage += 
                            '<div class="mt-4">' +
                            '<h5 class="font-medium text-gray-800 mb-3">Try These Alternatives:</h5>' +
                            '<div class="space-y-2">';
                        
                        data.suggestions.forEach(suggestion => {
                            alertMessage += 
                                '<div class="flex items-center p-3 bg-blue-50 rounded-lg">' +
                                '<i class="fas fa-lightbulb text-blue-500 mr-3"></i>' +
                                '<div>' +
                                '<div class="font-medium">' + suggestion.time + '</div>' +
                                '<div class="text-sm text-blue-700">' + suggestion.message + '</div>' +
                                '</div>' +
                                '</div>';
                        });
                        
                        alertMessage += '</div></div>';
                    }
                    
                    alertMessage += 
                        '<div class="mt-4 p-3 bg-yellow-50 rounded-lg">' +
                        '<p class="text-sm text-yellow-700">' +
                        '<i class="fas fa-exclamation-triangle mr-2"></i>' +
                        'After selecting an alternative, click "Verify Room Availability" again.' +
                        '</p>' +
                        '</div>';
                    
                    showAlert('error', alertMessage);
                }
            })
            .catch(err => {
                console.error('Error checking availability:', err);
                resultDiv.innerHTML = `
                    <div class="text-red-700 bg-red-50 border border-red-200 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                            <div>
                                <h4 class="font-semibold">Error Checking Availability</h4>
                                <p class="text-sm mt-1">Unable to verify room availability. Please try again.</p>
                            </div>
                        </div>
                    </div>
                `;
                isRoomAvailable = false;
                updateSubmitButtonStatus();
                
                showAlert('error',
                    '<div class="flex items-center mb-4">' +
                    '<div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center mr-3">' +
                    '<i class="fas fa-exclamation-triangle text-red-600"></i>' +
                    '</div>' +
                    '<div>' +
                    '<h4 class="font-semibold text-gray-800">Connection Error</h4>' +
                    '<p class="text-sm text-gray-600">Unable to check room availability. Please try again.</p>' +
                    '</div>' +
                    '</div>' +
                    '<div class="mt-4 p-3 bg-yellow-50 rounded-lg">' +
                    '<p class="text-sm text-yellow-700">' +
                    '<i class="fas fa-wifi mr-2"></i>' +
                    'Check your internet connection and try again. If the problem persists, contact support.' +
                    '</p>' +
                    '</div>'
                );
            });
    }

    // Update submit button status function
    function updateSubmitButtonStatus() {
        const submitBtn = document.getElementById('submitBtn');
        const submitStatus = document.getElementById('submitStatus');
        
        if (isRoomAvailable) {
            // Enable submit button
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            submitBtn.classList.add('hover:bg-indigo-700');
            
            // Update status text
            submitStatus.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Room verified ✓';
            submitStatus.className = 'ml-2 text-sm text-green-500 font-medium';
            
            // Add animation class
            submitBtn.classList.add('animate-pulse-subtle');
        } else {
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            submitBtn.classList.remove('hover:bg-indigo-700', 'animate-pulse-subtle');
            
            // Update status text
            submitStatus.innerHTML = '<i class="fas fa-lock mr-1"></i> Verify availability first';
            submitStatus.className = 'ml-2 text-sm text-yellow-600';
        }
    }

    // Form submission handler
    document.getElementById('bookingForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const date = document.querySelector('input[name="date"]:checked');
        const roomId = document.querySelector('input[name="room_id"]:checked');
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;

        // Basic validation
        if (!date || !roomId || !startTime || !endTime) {
            showAlert('error',
                '<div class="flex items-center mb-4">' +
                '<div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mr-4">' +
                '<i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>' +
                '</div>' +
                '<div>' +
                '<h4 class="text-lg font-semibold text-gray-800">Missing Information</h4>' +
                '<p class="text-gray-600">Please fill in all required fields:</p>' +
                '<ul class="mt-2 text-sm text-gray-600 list-disc list-inside">' +
                (!date ? '<li>Select a date</li>' : '') +
                (!roomId ? '<li>Choose a room</li>' : '') +
                (!startTime ? '<li>Select start time</li>' : '') +
                (!endTime ? '<li>Select end time</li>' : '') +
                '</ul>' +
                '</div>' +
                '</div>'
            );
            return;
        }

        // Time validation
        const timeValidation = validateTime();
        if (!timeValidation.valid) {
            showAlert('error',
                '<div class="flex items-center mb-4">' +
                '<div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mr-4">' +
                '<i class="fas fa-clock text-red-600 text-xl"></i>' +
                '</div>' +
                '<div>' +
                '<h4 class="text-lg font-semibold text-gray-800">Invalid Time Selection</h4>' +
                '<p class="text-gray-600">' + timeValidation.message + '</p>' +
                '</div>' +
                '</div>'
            );
            return;
        }

        // Check if availability has been verified
        if (!isRoomAvailable) {
            const alertHTML = 
                '<div class="flex items-center mb-4">' +
                '<div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center mr-4">' +
                '<i class="fas fa-lock text-red-600 text-xl"></i>' +
                '</div>' +
                '<div>' +
                '<h4 class="text-lg font-semibold text-gray-800">Availability Not Verified</h4>' +
                '<p class="text-gray-600">You must verify room availability before submitting.</p>' +
                '</div>' +
                '</div>' +
                '<div class="mt-4 p-4 bg-red-50 rounded-lg">' +
                '<p class="text-red-700">' +
                '<i class="fas fa-exclamation-circle mr-2"></i>' +
                '<strong>Important:</strong> The room might already be booked for your selected time.' +
                '</p>' +
                '</div>' +
                '<div class="mt-4">' +
                '<h5 class="font-medium text-gray-800 mb-2">Next Steps:</h5>' +
                '<ol class="list-decimal list-inside text-sm text-gray-600 space-y-2">' +
                '<li>Click the "Verify Room Availability" button below</li>' +
                '<li>If the room is available, you can submit your booking</li>' +
                '<li>If not available, choose a different time or room</li>' +
                '</ol>' +
                '</div>' +
                '<div class="mt-6">' +
                '<button onclick="checkAvailability()" class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center justify-center font-medium">' +
                '<i class="fas fa-search mr-2"></i> Verify Room Availability Now' +
                '</button>' +
                '</div>';
            
            showAlert('error', alertHTML);
            
            // Scroll to availability button
            document.querySelector('[onclick="checkAvailability()"]').scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
            
            // Highlight the availability button
            const availBtn = document.querySelector('[onclick="checkAvailability()"]');
            availBtn.classList.add('ring-4', 'ring-yellow-300', 'animate-pulse');
            setTimeout(() => {
                availBtn.classList.remove('ring-4', 'ring-yellow-300', 'animate-pulse');
            }, 3000);
            
            return;
        }

        // Show confirmation modal
        showConfirmationModal();
    });

    // Alert modal functions
    function showAlert(type, message) {
        let alertModal = document.getElementById('customAlertModal');
        if (!alertModal) {
            alertModal = document.createElement('div');
            alertModal.id = 'customAlertModal';
            alertModal.className = 'fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4';
            alertModal.innerHTML = `
                <div class="bg-white rounded-xl shadow-xl w-full max-w-lg transform transition-all">
                    <div class="p-6">
                        <div id="alertMessage"></div>
                        <div class="mt-6 flex justify-end">
                            <button onclick="closeAlert()" class="px-4 py-2 ${type === 'error' ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700'} text-white rounded-lg font-medium">
                                OK, I Understand
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(alertModal);
        }

        document.getElementById('alertMessage').innerHTML = message;
        alertModal.classList.remove('hidden');
        
        const escapeHandler = (e) => {
            if (e.key === 'Escape') closeAlert();
        };
        document.addEventListener('keydown', escapeHandler);
        alertModal._escapeHandler = escapeHandler;
    }

    function closeAlert() {
        const alertModal = document.getElementById('customAlertModal');
        if (alertModal) {
            alertModal.classList.add('hidden');
            if (alertModal._escapeHandler) {
                document.removeEventListener('keydown', alertModal._escapeHandler);
            }
        }
    }

    // Confirmation modal functions
    function showConfirmationModal() {
        const date = document.querySelector('input[name="date"]:checked').value;
        const roomName = document.querySelector('input[name="room_id"]:checked').nextElementSibling.querySelector('h5').textContent;
        const startTime = document.getElementById('start_time').value;
        const endTime = document.getElementById('end_time').value;
        const purpose = document.getElementById('purpose').value;

        let confirmModal = document.getElementById('confirmBookingModal');
        if (!confirmModal) {
            confirmModal = document.createElement('div');
            confirmModal.id = 'confirmBookingModal';
            confirmModal.className = 'fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4';
            confirmModal.innerHTML = `
                <div class="bg-white rounded-xl shadow-xl w-full max-w-lg transform transition-all">
                    <div class="p-6 border-b">
                        <div class="flex items-center justify-between">
                            <h3 class="text-xl font-semibold text-gray-800">Confirm Booking Request</h3>
                            <button onclick="closeConfirmationModal()" class="text-gray-500 hover:text-gray-700">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                                    <i class="fas fa-calendar text-blue-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-500">Date</p>
                                    <p class="font-medium" id="confirmDate"></p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-purple-50 flex items-center justify-center">
                                    <i class="fas fa-door-open text-purple-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-500">Room</p>
                                    <p class="font-medium" id="confirmRoom"></p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <div class="w-10 h-10 rounded-lg bg-green-50 flex items-center justify-center">
                                    <i class="fas fa-clock text-green-600"></i>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm text-gray-500">Time Slot</p>
                                    <p class="font-medium" id="confirmTime"></p>
                                </div>
                            </div>
                            ${purpose ? `
                                <div class="flex items-start">
                                    <div class="w-10 h-10 rounded-lg bg-yellow-50 flex items-center justify-center">
                                        <i class="fas fa-sticky-note text-yellow-600"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm text-gray-500">Purpose</p>
                                        <p class="font-medium">${purpose}</p>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-start">
                                <i class="fas fa-info-circle text-blue-500 mt-0.5 mr-3"></i>
                                <div>
                                    <p class="text-sm text-blue-700">This booking requires admin approval. You will be notified once it's approved or rejected.</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6 flex justify-end space-x-3">
                            <button onclick="closeConfirmationModal()" 
                                    class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium">
                                Cancel
                            </button>
                            <button onclick="submitBookingForm()" 
                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                                <i class="fas fa-paper-plane mr-2"></i> Submit Request
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(confirmModal);
        }

        const confirmDate = new Date(date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
        
        document.getElementById('confirmDate').textContent = confirmDate;
        document.getElementById('confirmRoom').textContent = roomName;
        document.getElementById('confirmTime').textContent = `${formatTime(startTime)} - ${formatTime(endTime)}`;

        confirmModal.classList.remove('hidden');
    }

    function closeConfirmationModal() {
        const confirmModal = document.getElementById('confirmBookingModal');
        if (confirmModal) {
            confirmModal.classList.add('hidden');
        }
    }

    function submitBookingForm() {
        // Remove animation class before submitting
        const submitBtn = document.getElementById('submitBtn');
        submitBtn.classList.remove('animate-pulse-subtle');
        
        // Submit the form
        document.getElementById('bookingForm').submit();
    }

    // Reset availability when form fields change
    document.querySelectorAll('input[name="date"]').forEach(radio => {
        radio.addEventListener('change', () => {
            isRoomAvailable = false;
            updateSubmitButtonStatus();
            document.getElementById('availabilityResult').classList.add('hidden');
        });
    });

    document.addEventListener('change', (e) => {
        if (e.target.name === 'room_id') {
            isRoomAvailable = false;
            updateSubmitButtonStatus();
            document.getElementById('availabilityResult').classList.add('hidden');
        }
    });

    document.getElementById('start_time').addEventListener('change', () => {
        isRoomAvailable = false;
        updateSubmitButtonStatus();
        document.getElementById('availabilityResult').classList.add('hidden');
    });

    document.getElementById('end_time').addEventListener('change', () => {
        isRoomAvailable = false;
        updateSubmitButtonStatus();
        document.getElementById('availabilityResult').classList.add('hidden');
    });

    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
        const dateInput = document.querySelector('input[name="date"]:checked');
        if (dateInput) {
            loadAvailableRooms();
        }
        validateTime();
        updateSubmitButtonStatus();
    });

    // Close modals when clicking outside
    document.addEventListener('click', (e) => {
        const alertModal = document.getElementById('customAlertModal');
        const confirmModal = document.getElementById('confirmBookingModal');
        
        if (alertModal && !alertModal.classList.contains('hidden')) {
            if (e.target === alertModal) {
                closeAlert();
            }
        }
        
        if (confirmModal && !confirmModal.classList.contains('hidden')) {
            if (e.target === confirmModal) {
                closeConfirmationModal();
            }
        }
    });
</script>

<style>
    @keyframes pulse-subtle {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.9;
        }
    }
    
    .animate-pulse-subtle {
        animation: pulse-subtle 2s ease-in-out infinite;
    }
</style>
@endsection
