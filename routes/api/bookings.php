<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RoomBookingController;
use App\Http\Controllers\Api\Admin\BookingController as AdminBookingController;

Route::prefix('bookings')->middleware('auth:sanctum')->group(function () {

    // Room Bookings
    Route::prefix('rooms')->group(function () {
        Route::get('/',             [RoomBookingController::class, 'index']);
        Route::post('/',            [RoomBookingController::class, 'store']);
        Route::get('/{id}',         [RoomBookingController::class, 'show']);
        Route::post('/{id}/cancel', [RoomBookingController::class, 'cancel']);
    });

});

Route::prefix('admin')->group(function () {

    // Bookings
    Route::prefix('bookings')->group(function () {
        Route::get('/',              [AdminBookingController::class, 'index']);
        Route::get('/{id}',          [AdminBookingController::class, 'show']);
        Route::patch('/{id}/status', [AdminBookingController::class, 'updateStatus']);
        Route::patch('/{id}/cancel', [AdminBookingController::class, 'cancel']);
    });

});