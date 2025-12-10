<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoomTypeController;
use Symfony\Component\HttpKernel\HttpCache\Store;

Route::prefix('roomtypes')->group(function () {
    Route::post('/store', [RoomTypeController::class, 'store']);
    Route::get('/index', [RoomTypeController::class, 'index']);
    Route::get('/{id}', [RoomTypeController::class, 'show']);
    Route::post('/{id}', [RoomTypeController::class, 'update']);
    Route::delete('/{id}', [RoomTypeController::class, 'destroy']);
});
