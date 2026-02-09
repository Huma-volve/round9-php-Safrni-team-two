<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TourActivity>
 */
class TourActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'tour_id'=> 1,
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(3),
            'image' => 'activities/default.jpg',
        ];
    }
}
