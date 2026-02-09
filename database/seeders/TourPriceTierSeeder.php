<?php

namespace Database\Seeders;

use App\Models\TourPriceTier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TourPriceTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TourPriceTier::factory()->count(10)->create();
    }
}
