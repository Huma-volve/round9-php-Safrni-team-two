<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{

    public function definition(): array
    {
        return [
            'brand_id' => $this->faker->numberBetween(1, 10),
            'categories' => $this->faker->randomElement(['sedan', 'suv', 'hatchback']),
            'model' => $this->faker->word(),
            'year' => $this->faker->year(),
            'seats_count' => $this->faker->numberBetween(2, 5),
            'doors' => $this->faker->numberBetween(2, 5),
            'fuel_type' => $this->faker->randomElement(['Gasoline', 'Diesel', 'Electric']),
            'transmission' => $this->faker->randomElement(['Automatic', 'Manual']),
            'luggage_capacity' => $this->faker->numberBetween(2, 5),
            'air_conditioning' => $this->faker->boolean(),
            'features' => $this->faker->words(3), // This is now fine!
            'images' => [$this->faker->imageUrl(), $this->faker->imageUrl()], // Array of images
            'license_requirements' => $this->faker->sentence(),
            'cancellation_policy' => $this->faker->sentence(),
            'location' => $this->faker->city(),
            'current_location_lat' => $this->faker->latitude(),
            'current_location_lng' => $this->faker->longitude(),
            'base_price_per_hour' => $this->faker->randomFloat(2, 10, 100),
            'is_available' => $this->faker->boolean(),
            'created_by' => \App\Models\User::factory(),
            
        ];
    }
}
