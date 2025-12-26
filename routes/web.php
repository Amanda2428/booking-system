<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoomTypeController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AdminDashboardController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('admidashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Bookings
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings');
    Route::get('/bookings/search', [BookingController::class, 'search']) ->name('bookings.search');
    Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
    Route::get('/bookings/{id}', [BookingController::class, 'show'])->name('bookings.show');
    Route::put('/bookings/{id}', [BookingController::class, 'update'])->name('bookings.update');
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy'])->name('bookings.destroy');

    // Rooms
    Route::get('/rooms', [RoomController::class, 'index'])->name('rooms');
    Route::get('/rooms/search', [RoomController::class, 'search'])->name('rooms.search');
    Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
    Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
    Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('rooms.edit');
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('rooms.destroy');

    // Categories
    Route::get('/categories', [RoomTypeController::class, 'index'])->name('categories');
    Route::post('/categories', [RoomTypeController::class, 'store'])->name('categories.store');
    Route::get('/categories/{id}', [RoomTypeController::class, 'show'])->name('categories.show');
    Route::put('/categories/{id}', [RoomTypeController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{id}', [RoomTypeController::class, 'destroy'])->name('categories.destroy');

    // Users
    Route::resource('users', UserController::class);

    // Feedback
    Route::get('/feedbacks', [FeedbackController::class, 'index'])->name('feedbacks');
});

Route::get('/', function () {
    if (Auth::check() && Auth::user()->role == 1) {
        return redirect()->route('admin.dashboard');
    }

    return view('welcome');
});

require __DIR__ . '/auth.php';
