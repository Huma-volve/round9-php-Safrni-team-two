<?php

namespace Database\Seeders;

<<<<<<< HEAD
use App\Models\Flight;
use App\Models\Review;
use App\Models\User;
=======
use App\Models\Review;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
>>>>>>> 0f192b0e788d514cd46aa7deb40e569a60a4a995
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
<<<<<<< HEAD
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
=======
        Review::factory()->count(20)->create();
>>>>>>> 0f192b0e788d514cd46aa7deb40e569a60a4a995
    }
}
