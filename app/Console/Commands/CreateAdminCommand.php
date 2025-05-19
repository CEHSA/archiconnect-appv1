<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create {--email=admin@architex.co.za : The email of the admin} {--password=password : The password for the admin} {--name=Administrator : The name of the admin}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->option('email');
        $password = $this->option('password');
        $name = $this->option('name');

        // Check if the admin already exists
        if (DB::table('admins')->where('email', $email)->exists()) {
            $this->error("Admin with email {$email} already exists.");
            return 1;
        }

        // Create the admin
        DB::table('admins')->insert([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->info("Admin user {$email} created successfully.");
        return 0;
    }
}
