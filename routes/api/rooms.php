<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\Admin\RoomController as AdminRoomController;

// =====================================================
// Public Room Routes
// =====================================================
Route::prefix('rooms')->group(function () {

    Route::get('/{id}', [RoomController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('api.rooms.show');

    Route::post('/{id}/check-availability', [RoomController::class, 'checkAvailability'])
        ->name('api.rooms.check-availability');
});

// =====================================================
// Admin Room Routes
// =====================================================
Route::prefix('admin/hotels/{hotelId}/rooms')->group(function () {
    Route::post('/',       [AdminRoomController::class, 'store']);
    Route::put('/{id}',    [AdminRoomController::class, 'update']);
    Route::delete('/{id}', [AdminRoomController::class, 'destroy']);
});