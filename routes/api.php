<?php

use App\Http\Controllers\Api\Tour\TourController;
use App\Http\Controllers\Api\Tour\TourDetailsController;
use App\Http\Controllers\Api\Tour\TourFavoriteController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/tours', [TourController::class, 'index']);

Route::get('/tour/{id}', [TourDetailsController::class, 'show']);


Route::get('/tours/favorites', [TourFavoriteController::class, 'index']);

Route::post('/tours/favorites', [TourFavoriteController::class, 'store']);

Route::delete('/tours/favorites', [TourFavoriteController::class, 'destroy']);

