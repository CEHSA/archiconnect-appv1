<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the user first
        $user = User::create([
            'name' => 'Admin User',
            'email' => 'admin@architex.co.za',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'role' => 'admin'
        ]);

        // Then create the admin record associated with the user
        Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@architex.co.za',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'user_id' => $user->id
        ]);
    }
}
