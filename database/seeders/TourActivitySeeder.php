<?php

namespace Database\Seeders;

use App\Models\TourActivity;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TourActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TourActivity::factory()->count(10)->create();
    }
}
