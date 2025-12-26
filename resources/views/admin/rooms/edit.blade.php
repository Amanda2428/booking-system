@extends('layouts.admin')

@section('title', 'Edit Room')
@section('subtitle', 'Update room information')

@section('breadcrumb')
    <li><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li><a href="{{ route('admin.rooms') }}">Rooms</a></li>
    <li class="text-gray-500">Edit Room</li>
@endsection

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-sm border">
            <div class="p-6 border-b">
                <h2 class="text-xl font-semibold text-gray-800">Edit Room</h2>
                <p class="text-gray-600 mt-1">Update the details of {{ $room->name }}</p>
            </div>
            
            <form action="{{ route('admin.rooms.update', $room->id) }}" method="POST" class="p-6">
                @csrf
                @method('PUT')
                
                <div class="space-y-6">
                    <!-- Room Information -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-800 mb-4">Room Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Room Name *
                                </label>
                                <input type="text" 
                                       id="name" 
                                       name="name" 
                                       value="{{ $room->name }}"
                                       required
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            
                            <div>
                                <label for="capacity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Capacity *
                                </label>
                                <input type="number" 
                                       id="capacity" 
                                       name="capacity" 
                                       value="{{ $room->capacity }}"
                                       required
                                       min="1"
                                       max="100"
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                    Location *
                                </label>
                                <input type="text" 
                                       id="location" 
                                       name="location" 
                                       value="{{ $room->location }}"
                                       required
                                       class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            </div>
                            
                            <div>
                                <label for="room_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Category *
                                </label>
                                <select id="room_type_id" 
                                        name="room_type_id" 
                                        required
                                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <option value="">Select Category</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $room->room_type_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ $room->description }}</textarea>
                    </div>
                    
                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Availability Status *
                        </label>
                        <div class="flex space-x-4">
                            <label class="inline-flex items-center">
                                <input type="radio" 
                                       name="availability_status" 
                                       value="available"
                                       {{ $room->availability_status == 'available' ? 'checked' : '' }}
                                       class="text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2">Available</span>
                            </label>
                            <label class="inline-flex items-center">
                                <input type="radio" 
                                       name="availability_status" 
                                       value="unavailable"
                                       {{ $room->availability_status == 'unavailable' ? 'checked' : '' }}
                                       class="text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2">Unavailable</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Submit -->
                    <div class="pt-6 border-t flex justify-end space-x-3">
                        <a href="{{ route('admin.rooms') }}" 
                           class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 flex items-center">
                            <i class="fas fa-save mr-2"></i> Update Room
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection