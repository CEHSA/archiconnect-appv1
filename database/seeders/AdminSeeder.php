<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@architex.co.za', // Changed email
            'password' => Hash::make('password'), // Change this in a real application!
            'email_verified_at' => now(),
        ]);
    }
}
