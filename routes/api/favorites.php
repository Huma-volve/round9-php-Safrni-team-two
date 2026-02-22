<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FavoriteController;

Route::prefix('favorites')->middleware('auth:sanctum')->group(function () {
    Route::get('/',       [FavoriteController::class, 'index']);   // قائمة المفضلة
    Route::post('/',      [FavoriteController::class, 'toggle']);  // إضافة/إزالة
    Route::delete('/{id}',[FavoriteController::class, 'destroy']); // حذف بالـ ID
});
