<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

// Check if admin already exists
$admin = DB::table('admins')->where('email', 'admin@architex.co.za')->first();

if ($admin) {
    echo "Admin with email admin@architex.co.za already exists.\n";
} else {
    // Insert new admin
    DB::table('admins')->insert([
        'name' => 'Admin User',
        'email' => 'admin@architex.co.za', // Changed email
        'password' => Hash::make('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    
    echo "Admin created successfully with:\n";
    echo "Email: admin@architex.co.za\n";
    echo "Password: password\n";
}
