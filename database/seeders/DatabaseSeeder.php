<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AirportSeeder::class,
            AircraftSeeder::class,
            FlightSeeder::class,
            FlightFareSeeder::class,
            SeatSeeder::class,
            ReviewSeeder::class,
            // BookingSeeder can be skipped for now or added later
        ]);
    }
}
