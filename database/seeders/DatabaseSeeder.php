<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call(TourSeeder::class);
        $this->call(TourPriceTierSeeder::class);
        $this->call(TourScheduleSeeder::class);
        $this->call(TourImageSeeder::class);
        $this->call(TourActivitySeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ReviewSeeder::class);  
        $this->call(FavoriteSeeder::class); 
    }
}
