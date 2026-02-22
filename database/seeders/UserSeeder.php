<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name'              => 'Admin',
            'email'             => 'admin@safarni.com',
            'password'          => Hash::make('password123'),
            'email_verified_at' => now(),
            
        ]);

        // Normal User
        User::create([
            'name'              => 'Test User',
            'email'             => 'user@safarni.com',
            'password'          => Hash::make('password123'),
            'email_verified_at' => now(),
            
        ]);
    }
}