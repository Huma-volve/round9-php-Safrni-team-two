<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tour>
 */
class TourFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence(3);
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->paragraph(4),
            'main_image' => 'tours/default.jpg',
            'duration' => $this->faker->randomElement([
                'half_day',
                'full_day',
                'multi_day',
                'night_tour'
            ]),
            'location' => $this->faker->city,
            'stars' => $this->faker->numberBetween(3, 5),
            'recommended' => $this->faker->boolean(30),
            'created_by' => 1, // admin user
        ];
    }
}
