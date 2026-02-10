<?php

namespace Database\Factories;

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
            'tour_id' => 1,
            'start_date' => now()->addDays(rand(1, 30)),
            'end_date' => now()->addDays(rand(31, 40)),
            'capacity' => $capacity,
            'available_slots' => $capacity,
            'price_tier_id'=> 1, 
            'best_time_visit'=> "Spring and Autumn are the best times to visit for pleasant weather and fewer crowds.",
        ];
    }
}
