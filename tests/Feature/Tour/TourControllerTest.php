<?php

use App\Models\Tour;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('returns paginated tours without search', function () {
    // جهز بيانات تجريبية
    Tour::factory()->count(5)->create();

    $response = $this->getJson('/api/tours');

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
             ])
             ->assertJsonStructure([
                 'success',
                 'data' => [
                     'current_page',
                     'data' => [
                         '*' => [
                             'id',
                             'title',
                             'slug',
                             'description',
                             'main_image',
                             'duration',
                             'location',
                             'stars',
                             'recommended',
                             'created_at',
                             'updated_at',
                             'adult_price',
                         ]
                     ],
                     'per_page',
                     'total'
                 ],
             ]);
});

it('filters tours by search term', function () {
    Tour::factory()->create(['title' => 'Amazing Safari']);
    Tour::factory()->create(['title' => 'Desert Adventure']);

    $response = $this->getJson('/api/tours?search=Safari');

    $response->assertStatus(200)
             ->assertJsonCount(1, 'data.data')
             ->assertJsonFragment(['title' => 'Amazing Safari']);
});

it('returns validation error for short search', function () {
    $response = $this->getJson('/api/tours?search=A');

    $response->assertStatus(422)
             ->assertJson([
                 'success' => false,
                 'error' => [
                     'code' => 'VALIDATION_ERROR',
                 ],
             ]);
});
