<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\OtpController;
use App\Http\Controllers\Api\V1\Auth\PasswordController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\GoogleAuthController;
use App\Http\Controllers\Api\V1\Payments\PaymentsController;
use App\Http\Controllers\Api\V1\Profile\ProfileController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// =====================================================
// Safarni Routes
// =====================================================
Route::prefix('safarni')->group(function () {
    require __DIR__ . '/api/hotels.php';
    require __DIR__ . '/api/rooms.php';
    require __DIR__ . '/api/bookings.php';
    require __DIR__ . '/api/reviews.php';
    require __DIR__ . '/api/favorites.php';
}); // ← الإغلاق هنا

// =====================================================
// V1 Auth Routes
// =====================================================
Route::prefix('v1')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::post('register', [RegisterController::class, 'register']);
        Route::post('login', [LoginController::class, 'login']);

        Route::post('verify-otp', [OtpController::class, 'verifyOtp']);
        Route::post('resend-otp', [OtpController::class, 'resendOtp']);

        Route::post('forgot-password', [PasswordController::class, 'forgot']);
        Route::post('reset-password', [PasswordController::class, 'reset']);

        Route::get('google/url', [GoogleAuthController::class, 'url']);
        Route::post('google/exchange', [GoogleAuthController::class, 'exchange']);
    });

    Route::middleware('auth:sanctum')->group(function () {

        Route::prefix('auth')->group(function () {
            Route::post('logout', [LogoutController::class, 'logout']);
        });

        Route::get('users/me', [ProfileController::class, 'show']);
        Route::post('users/me', [ProfileController::class, 'update']);
        Route::delete('users/me', [ProfileController::class, 'destroy']);

        Route::post('payments/initiate', [PaymentsController::class, 'initiate']);
    });
});