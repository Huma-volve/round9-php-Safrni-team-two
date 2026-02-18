<?php

namespace Database\Factories;

use App\Models\Tour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TourPriceTier>
 */
class TourPriceTierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "tour_id" => Tour::inRandomOrder()->value('id'),
            'name' => $this->faker->randomElement(['Standard', 'VIP', 'Premium']),
            'adult_price' => $this->faker->numberBetween(50, 200),
            'child_price' => $this->faker->numberBetween(20, 100),
            'infant_price' => 0,
        ];
    }
}
