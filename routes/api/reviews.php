<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\Admin\ReviewController as AdminReviewController;

// Public: عرض الريفيوز
Route::get('/{type}/{id}/reviews', [ReviewController::class, 'index']);

// User Routes
Route::group(function () {
    Route::post('/reviews',              [ReviewController::class, 'store']);
    Route::delete('/reviews/{id}',       [ReviewController::class, 'destroy']);
    Route::post('/reviews/{id}/helpful', [ReviewController::class, 'markHelpful']);
});

// =====================================================
// Admin Review Routes
// =====================================================
Route::prefix('admin/reviews')->group(function () {
    Route::get('/',                [AdminReviewController::class, 'index']);
    Route::patch('/{id}/approve',  [AdminReviewController::class, 'approve']);
    Route::patch('/{id}/reject',   [AdminReviewController::class, 'reject']);
    Route::delete('/{id}',         [AdminReviewController::class, 'destroy']);
});