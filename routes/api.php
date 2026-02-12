<?php

use App\Http\Controllers\Api\BookingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FlightController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// User Routes
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('bookings')->group(function () {
    Route::post('/', [BookingController::class, 'store'])->name('bookings.store');
});

// Flight Public Routes
Route::prefix('flights')->group(function () {
    Route::get('/search', [FlightController::class, 'search'])->name('flights.search');
    Route::get('/compare', [FlightController::class, 'compare'])->name('flights.compare');
    Route::get('/{id}', [FlightController::class, 'show'])->name('flights.show');
    Route::get('/{id}/seats', [FlightController::class, 'getSeats'])->name('flights.seats');
});
