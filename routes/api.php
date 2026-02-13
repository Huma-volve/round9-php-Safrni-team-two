<?php

use App\Http\Controllers\Api\Tour\TourBookingController;
use App\Http\Controllers\Api\Tour\TourController;
use App\Http\Controllers\Api\Tour\TourDetailsController;
use App\Http\Controllers\Api\Tour\TourFavoriteController;
use App\Http\Controllers\Api\Tour\TourPaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

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
