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
<<<<<<< HEAD
        $this->call([
            AirportSeeder::class,
            AircraftSeeder::class,
            SeatSeeder::class,
            FlightSeeder::class,
            FlightFareSeeder::class,
            ReviewSeeder::class,
            // BookingSeeder can be skipped for now or added later
=======

        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
>>>>>>> 0f192b0e788d514cd46aa7deb40e569a60a4a995
        ]);

        $this->call(TourSeeder::class);
        $this->call(TourPriceTierSeeder::class);
        $this->call(TourScheduleSeeder::class);
        $this->call(TourImageSeeder::class);
        $this->call(TourActivitySeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ReviewSeeder::class);
        $this->call(FavoriteSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(HotelSeeder::class);
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
