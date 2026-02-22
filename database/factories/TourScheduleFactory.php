<?php

namespace Database\Factories;

use App\Models\Tour;
use App\Models\TourPriceTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TourSchedule>
 */
class TourScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $capacity = $this->faker->numberBetween(10, 30);

        return [
            'tour_id' => Tour::inRandomOrder()->value('id'),
            'start_date' => now()->addDays(rand(1, 30)),
            'end_date' => now()->addDays(rand(31, 40)),
            'capacity' => $capacity,
            'available_slots' => $capacity,
            'price_tier_id'=> TourPriceTier::inRandomOrder()->value('id'),
            'best_time_visit'=> "Spring and Autumn are the best times to visit for pleasant weather and fewer crowds.",
        ];
    }
}
