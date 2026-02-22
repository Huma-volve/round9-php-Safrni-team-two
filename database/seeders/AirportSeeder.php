<?php

namespace Database\Seeders;

use App\Models\Airport;
use Illuminate\Database\Seeder;

class AirportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add some major airports
        $airports = [
            ['airport_code' => 'CAI', 'airport_name' => 'Cairo International Airport', 'city' => 'Cairo', 'country' => 'Egypt'],
            ['airport_code' => 'DXB', 'airport_name' => 'Dubai International Airport', 'city' => 'Dubai', 'country' => 'UAE'],
            ['airport_code' => 'LHR', 'airport_name' => 'Heathrow Airport', 'city' => 'London', 'country' => 'UK'],
            ['airport_code' => 'JFK', 'airport_name' => 'John F. Kennedy International Airport', 'city' => 'New York', 'country' => 'USA'],
            ['airport_code' => 'HND', 'airport_name' => 'Haneda Airport', 'city' => 'Tokyo', 'country' => 'Japan'],
            ['airport_code' => 'CDG', 'airport_name' => 'Charles de Gaulle Airport', 'city' => 'Paris', 'country' => 'France'],
            ['airport_code' => 'IST', 'airport_name' => 'Istanbul Airport', 'city' => 'Istanbul', 'country' => 'Turkey'],
            ['airport_code' => 'RUH', 'airport_name' => 'King Khalid International Airport', 'city' => 'Riyadh', 'country' => 'Saudi Arabia'],
            ['airport_code' => 'JED', 'airport_name' => 'King Abdulaziz International Airport', 'city' => 'Jeddah', 'country' => 'Saudi Arabia'],
        ];

        foreach ($airports as $airport) {
            Airport::firstOrCreate(['airport_code' => $airport['airport_code']], $airport);
        }
    }
}
