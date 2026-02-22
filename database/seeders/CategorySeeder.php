<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['key' => 'tour', 'title' => 'Tour', 'description' => 'Tour category'],
            ['key' => 'flight', 'title' => 'Flight', 'description' => 'Flight category'],
            ['key' => 'car', 'title' => 'Car', 'description' => 'Car category'],
            ['key' => 'hotel', 'title' => 'Hotel', 'description' => 'Hotel category'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}
