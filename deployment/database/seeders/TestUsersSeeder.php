<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create freelancer test user
        User::create([
            'name' => 'Test Freelancer',
            'email' => 'freelancer@architex.co.za', // Changed email
            'password' => Hash::make('password'),
            'role' => User::ROLE_FREELANCER,
            'email_verified_at' => now(),
        ]);

        // Create client test users
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name' => "Test Client $i",
                'email' => "client$i@architex.co.za", // Changed email
                'password' => Hash::make('password'),
                'role' => User::ROLE_CLIENT,
                'email_verified_at' => now(),
            ]);
        }
    }
}
