<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected $model = Review::class;

    public function definition(): array
    {
        return [
            'user_id' => 1, 
            'category_id' => Category::inRandomOrder()->value('id'), 
            'item_id' => $this->faker->numberBetween(1, 10),
            'rating' => $this->faker->numberBetween(1, 5),
            'title' => $this->faker->sentence,
            'body' => $this->faker->paragraph,
            'photos_json' => json_encode([$this->faker->imageUrl(), $this->faker->imageUrl()]),
        ];
    }
}
