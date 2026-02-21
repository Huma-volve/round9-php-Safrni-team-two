<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('safarni')->group(function () {
    
    require __DIR__ . '/api/hotels.php';
    require __DIR__ . '/api/rooms.php';
    require __DIR__ . '/api/bookings.php';
    require __DIR__ . '/api/reviews.php';
     require __DIR__ . '/api/favorites.php';




});
