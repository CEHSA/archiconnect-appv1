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
        // Create Admin User
        User::firstOrCreate(
            ['email' => 'admin@architex.co.za'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_ADMIN,
                'email_verified_at' => now(),
            ]
        );

        // Create Freelancer User
        User::firstOrCreate(
            ['email' => 'freelancer@architex.co.za'],
            [
                'name' => 'Freelancer User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_FREELANCER,
                'email_verified_at' => now(),
            ]
        );

        // Create Client User
        User::firstOrCreate(
            ['email' => 'client@architex.co.za'],
            [
                'name' => 'Client User',
                'password' => Hash::make('password'),
                'role' => User::ROLE_CLIENT,
                'email_verified_at' => now(),
            ]
        );
    }
}
