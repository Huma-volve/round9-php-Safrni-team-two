<?php

use App\Http\Controllers\Api\Tour\TourController;
use App\Http\Controllers\Api\Tour\TourDetailsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/tours',[TourController::class,'index']);

Route::get('/tour/{id}',[TourDetailsController::class,'show']);