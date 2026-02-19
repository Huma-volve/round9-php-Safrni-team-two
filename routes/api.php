<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\OtpController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\GoogleAuthController;
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\FlightController;

use App\Http\Controllers\Api\V1\Profile\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [RegisterController::class, 'register']);
        Route::post('login', [LoginController::class, 'login']);

        Route::post('verify-otp', [OtpController::class, 'verifyOtp']);
        Route::post('resend-otp', [OtpController::class, 'resendOtp']);

        Route::post('forgot-password', [PasswordController::class, 'forgot']);
        Route::post('reset-password', [PasswordController::class, 'reset']);

        // Google Routes
        Route::get('google/url', [GoogleAuthController::class, 'url']);
        Route::post('google/exchange', [GoogleAuthController::class, 'exchange']);
    });


    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('auth')->group(function () {
            Route::post('logout', [LogoutController::class, 'logout']);
        });


        // Profile routes

        Route::get('users/me', [ProfileController::class, 'show']);
        Route::post('users/me', [ProfileController::class, 'update']);
        Route::delete('users/me', [ProfileController::class, 'destroy']);
    });
});


Route::post('bookings/', [BookingController::class, 'store'])->name('bookings.store')->middleware('auth:sanctum');

// Flight Public Routes
Route::prefix('flights')->group(function () {
    Route::get('/search', [FlightController::class, 'search'])->name('flights.search');
    Route::get('/compare', [FlightController::class, 'compare'])->name('flights.compare');
    Route::get('/{id}', [FlightController::class, 'show'])->name('flights.show');
    Route::get('/{id}/seats', [FlightController::class, 'getSeats'])->name('flights.seats');
});
