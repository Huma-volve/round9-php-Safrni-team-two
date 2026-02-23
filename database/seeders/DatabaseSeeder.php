<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Factories
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seeders
        $this->call([
            AirportSeeder::class,
            AircraftSeeder::class,
            SeatSeeder::class,
            FlightSeeder::class,
            FlightFareSeeder::class,
            ReviewSeeder::class,
            TourSeeder::class,
            TourPriceTierSeeder::class,
            TourScheduleSeeder::class,
            TourImageSeeder::class,
            TourActivitySeeder::class,
            CategorySeeder::class,
            FavoriteSeeder::class,
            UserSeeder::class,
            HotelSeeder::class,
        ]);
    }
}
