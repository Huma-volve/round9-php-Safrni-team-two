<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Api\RoomController;
use App\Http\Controllers\Api\Admin\HotelController as AdminHotelController;

// =====================================================
// Public Hotel Routes
// =====================================================
Route::prefix('hotels')->group(function () {

    Route::get('/',            [HotelController::class, 'index'])->name('api.hotels.index');
    Route::get('/recommended', [HotelController::class, 'recommended'])->name('api.hotels.recommended');
    Route::get('/featured',    [HotelController::class, 'featured'])->name('api.hotels.featured');
    Route::get('/nearby',      [HotelController::class, 'nearby'])->name('api.hotels.nearby');

    Route::get('/{id}',        [HotelController::class, 'show'])
        ->where('id', '[0-9]+')
        ->name('api.hotels.show');

    Route::get('/slug/{slug}', [HotelController::class, 'showBySlug'])
        ->name('api.hotels.show-by-slug');

    Route::post('/{id}/check-availability', [HotelController::class, 'checkAvailability'])
        ->name('api.hotels.check-availability');

    // Rooms under a hotel
    Route::prefix('{hotelId}/rooms')->group(function () {
        Route::get('/',        [RoomController::class, 'index'])->name('api.hotels.rooms.index');
        Route::get('/{slug}',  [RoomController::class, 'showBySlug'])->name('api.hotels.rooms.show-by-slug');
    });
});

// =====================================================
// Admin Hotel Routes
// =====================================================
Route::prefix('admin/hotels')->group(function () {
    Route::post('/',       [AdminHotelController::class, 'store']);
    Route::put('/{id}',    [AdminHotelController::class, 'update']);
    Route::delete('/{id}', [AdminHotelController::class, 'destroy']);
});