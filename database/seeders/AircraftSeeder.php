<?php

namespace Database\Seeders;

use App\Models\Aircraft;
use Illuminate\Database\Seeder;

class AircraftSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add some aircraft models
        $aircrafts = [
            ['model' => 'Boeing 737', 'manufacturer' => 'Boeing', 'total_seats' => 10], // Small for testing seats
            ['model' => 'Airbus A320', 'manufacturer' => 'Airbus', 'total_seats' => 12],
            ['model' => 'Boeing 777', 'manufacturer' => 'Boeing', 'total_seats' => 15],
        ];

        foreach ($aircrafts as $aircraft) {
            Aircraft::firstOrCreate(['model' => $aircraft['model']], $aircraft);
        }
    }
}
