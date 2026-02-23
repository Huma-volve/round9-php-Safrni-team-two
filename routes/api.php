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
use App\Http\Controllers\Api\BookingController;
use App\Http\Controllers\Api\FlightController;
use App\Http\Controllers\Api\V1\Payments\PaymentsController;
use App\Http\Controllers\Api\V1\Profile\ProfileController;

use App\Http\Controllers\Cars\BookingController as CarBookingController;
use App\Http\Controllers\Cars\CarController;
use App\Http\Controllers\Cars\CarFavouriteController;
use App\Http\Controllers\Cars\CarReviewController;
use Illuminate\Http\Request;
use Illuminate\Queue\Connectors\FailoverConnector;
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

Route::post('bookings/', [BookingController::class, 'store'])->name('bookings.store')->middleware('auth:sanctum');

// Flight Public Routes
Route::prefix('flights')->group(function () {
    Route::get('/search', [FlightController::class, 'search'])->name('flights.search');
    Route::get('/compare', [FlightController::class, 'compare'])->name('flights.compare');
    Route::get('/{id}', [FlightController::class, 'show'])->name('flights.show');
    Route::get('/{id}/seats', [FlightController::class, 'getSeats'])->name('flights.seats');
});


//       TOUR 
Route::get('/tours', [TourController::class, 'index']);

Route::get('/tour/{id}', [TourDetailsController::class, 'show']);


Route::middleware('auth:sanctum')->group(function () {

    Route::get('/tours/favorites', [TourFavoriteController::class, 'index']);

    Route::post('/tours/favorites', [TourFavoriteController::class, 'store']);

    Route::delete('/tours/favorites', [TourFavoriteController::class, 'destroy']);

    Route::post('tours/{id}/check-availability', [TourBookingController::class, 'checkAvailability']);

    Route::post('tours/{id}/booking', [TourBookingController::class, 'booking']);

    Route::get('tours/{id}/booking/show', [TourBookingController::class, 'show']);
});

Route::prefix('cars')->group(function () {

    Route::get('/', [CarController::class, 'index']);
    Route::get('{car}', [CarController::class, 'show']);
    Route::post('compare', [CarController::class, 'compare']);
    Route::get('{car}/pricing', [CarBookingController::class, 'calculatePricing']);
    Route::post('bookings/calculate', [CarBookingController::class, 'calculateTotal']);
    Route::post('bookings', [CarBookingController::class, 'store']);

    // review routes
    Route::get('/cars/{car}/reviews', [CarReviewController::class, 'index']);
    Route::post('/reviews', [CarReviewController::class, 'store']);

    // fav routes 
    Route::get('/favorites', [CarFavouriteController::class, 'index']);
    Route::post('/favorites', [CarFavouriteController::class, 'store']);
    Route::delete('/favorites/{carId}', [CarFavouriteController::class, 'destroy']);
});
