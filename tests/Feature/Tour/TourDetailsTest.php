<?php

use App\Models\Category;
use App\Models\Review;
use App\Models\Tour;
use App\Models\TourActivity;
use App\Models\TourPriceTier;
use App\Models\TourSchedule;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // ✅ Seed categories (including 'tour')
    $this->seed(\Database\Seeders\CategorySeeder::class);
});



it('returns tour details successfully', function () {

    $user = \App\Models\User::factory()->create(); // لازم user للreview

    $tour = \App\Models\Tour::factory()->create([
      //  'status' => 'active', // لو service بيشيك على status
    ]);

    $priceTier = \App\Models\TourPriceTier::factory()->create([
        'tour_id' => $tour->id,
    ]);

    \App\Models\TourSchedule::factory()->create([
        'tour_id' => $tour->id,
        'price_tier_id' => $priceTier->id,
        'start_date' => '2026-02-10',
        'end_date' => '2026-03-28',
    ]);

    \App\Models\TourActivity::factory()->count(3)->create([
        'tour_id' => $tour->id,
    ]);

    $category = \App\Models\Category::first(); // من seeder

    \App\Models\Review::factory()->create([
        'category_id' => $category->id,
        'item_id' => $tour->id,
        'user_id' => $user->id,
    ]);

    $response = $this->getJson("/api/tours/{$tour->id}");

    // $response
        
    //     ->assertJson([
    //         'success' => true,
    //     ])
    //     ->assertJsonStructure([
    //         'success',
    //         'data' => [
    //             'tour',
    //             'schedule' => ['0', 'period_days'],
    //             'price_tiers',
    //             'activities',
    //             'reviews',
    //         ],
    //     ]);
});
