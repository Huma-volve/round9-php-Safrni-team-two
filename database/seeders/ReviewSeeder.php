<?php

namespace Database\Seeders;

use App\Models\Flight;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure we have a user
        $user = User::first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $flights = Flight::all();

        foreach ($flights as $flight) {
            Review::create([
                'flight_id' => $flight->id,
                'user_id' => $user->id,
                'rating' => rand(3, 5),
                'comment' => 'Great flight experience!',
            ]);
        }
    }
}
