<?php

namespace Database\Seeders;

use App\Models\Flight;
use App\Models\FlightFare;
use Illuminate\Database\Seeder;

class FlightFareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $flights = Flight::all();

        foreach ($flights as $flight) {
            // Economy Fare
            FlightFare::create([
                'flight_id' => $flight->id,
                'class_type' => 'economy',
                'base_price' => rand(3000, 6000), // Random base price betwen 3000 and 6000 EGP
                'taxes' => 500,
                'baggage_price' => 0, // Included
                'seats_available' => 100,
                'stops' => 0,
                'is_refundable' => $flight->refundability,
            ]);

            // Business Fare
            FlightFare::create([
                'flight_id' => $flight->id,
                'class_type' => 'business',
                'base_price' => rand(10000, 20000),
                'taxes' => 1500,
                'baggage_price' => 0,
                'seats_available' => 20,
                'stops' => 0,
                'is_refundable' => true,
            ]);
        }
    }
}
