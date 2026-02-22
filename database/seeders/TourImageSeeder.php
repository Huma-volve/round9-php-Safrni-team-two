<?php

namespace Database\Seeders;

use App\Models\TourImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TourImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TourImage::factory()->count(10)->create();
    }
}
