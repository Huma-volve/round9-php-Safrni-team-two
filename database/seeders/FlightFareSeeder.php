<?php

namespace Database\Seeders;

use App\Models\Flight;
use App\Models\FlightFare;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FlightFareSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $flights = Flight::with(['aircraft.seats'])->get();

        foreach ($flights as $flight) {
            if (! $flight->aircraft) {
                continue;
            }

            // Calculate seats dynamically based on actual seats table
            $aircraft = $flight->aircraft;

            $businessSeats = $aircraft->seats->where('class_type', 'business')->count();
            $economySeats = $aircraft->seats->where('class_type', 'economy')->count();
            
            // Economy Fare
            FlightFare::create([
                'flight_id' => $flight->id,
                'class_type' => 'economy',
                'base_price' => rand(3000, 6000), // Random base price between 3000 and 6000 EGP
                'taxes' => 500,
                'baggage_price' => 0, // Included
                'seats_available' => $economySeats,
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
                'seats_available' => $businessSeats,
                'stops' => 0,
                'is_refundable' => true,
            ]);
        }
    }
}
