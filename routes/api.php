<?php

use App\Http\Controllers\Api\Tour\TourBookingController;
use App\Http\Controllers\Api\Tour\TourController;
use App\Http\Controllers\Api\Tour\TourDetailsController;
use App\Http\Controllers\Api\Tour\TourFavoriteController;

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

        // Payments routes
        Route::post('payments/initiate', [PaymentsController::class, 'initiate']);
    });
});



//       TOUR 
Route::get('/tours', [TourController::class, 'index']);

Route::get('/tour/{id}', [TourDetailsController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {

/*     Route::get('/tours/favorites', [TourFavoriteController::class, 'index']);

    Route::post('/tours/favorites', [TourFavoriteController::class, 'store']);

    Route::delete('/tours/favorites', [TourFavoriteController::class, 'destroy']);

    Route::post('tours/{id}/check-availability', [TourBookingController::class, 'checkAvailability']);

    Route::post('tours/{id}/booking', [TourBookingController::class, 'booking']);

    Route::get('tours/{id}/booking/show', [TourBookingController::class, 'show']); */
});
    Route::get('/tours/favorites', [TourFavoriteController::class, 'index']);

    Route::post('/tours/favorites', [TourFavoriteController::class, 'store']);

    Route::delete('/tours/favorites', [TourFavoriteController::class, 'destroy']);

    Route::post('tours/{id}/check-availability', [TourBookingController::class, 'checkAvailability']);

    Route::post('tours/{id}/booking', [TourBookingController::class, 'booking']);

    Route::get('tours/{id}/booking/show', [TourBookingController::class, 'show']);