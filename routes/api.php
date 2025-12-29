<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoomTypeController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\UserController;
use Symfony\Component\HttpKernel\HttpCache\Store;

Route::prefix('roomtypes')->group(function () {
    Route::post('/store', [RoomTypeController::class, 'store']);
    Route::get('/index', [RoomTypeController::class, 'index']);
    Route::get('/{id}', [RoomTypeController::class, 'show']);
    Route::post('/{id}', [RoomTypeController::class, 'update']);
    Route::delete('/{id}', [RoomTypeController::class, 'destroy']);
});

Route::prefix('rooms')->group(function () {
    Route::get('/', [RoomController::class, 'index']);
    Route::get('{id}', [RoomController::class, 'show']);
    Route::post('/', [RoomController::class, 'store']);
    Route::post('{id}', [RoomController::class, 'update']);
    Route::delete('{id}', [RoomController::class, 'destroy']);
});

Route::prefix('feedbacks')->group(function () {
    Route::get('/', [FeedbackController::class, 'index']);
    Route::get('{id}', [FeedbackController::class, 'show']);
    Route::post('/', [FeedbackController::class, 'store']);
    Route::post('{id}', [FeedbackController::class, 'update']);
    Route::delete('{id}', [FeedbackController::class, 'destroy']);
});

Route::prefix('bookings')->group(function () {
    Route::get('/', [BookingController::class, 'index']);
    Route::get('/bookings/{id}', [BookingController::class, 'show']);
    Route::post('/', [BookingController::class, 'store']);
    Route::put('/bookings/{id}', [BookingController::class, 'update']);
    Route::delete('{id}', [BookingController::class, 'destroy']);
     Route::post('/bookings/check-availability', [BookingController::class, 'checkAvailability']);
});
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index']);
    Route::post('/', [UserController::class, 'store']);
    Route::get('{id}', [UserController::class, 'show']);
    Route::post('{id}', [UserController::class, 'update']);
    Route::delete('{id}', [UserController::class, 'destroy']);
});
