<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@architex.co.za', // Changed email
            'password' => Hash::make('password'), // Change this in a real application!
            'role' => User::ROLE_ADMIN,
            'email_verified_at' => now(),
        ]);
    }
}
