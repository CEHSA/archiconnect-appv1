<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_role_methods()
    {
        $adminUser = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => 'password',
            'role' => 'admin'
        ]);

        $clientUser = User::create([
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'password' => 'password',
            'role' => 'client'
        ]);

        $freelancerUser = User::create([
            'name' => 'Test Freelancer',
            'email' => 'freelancer@test.com',
            'password' => 'password',
            'role' => 'freelancer'
        ]);

        $this->assertTrue($adminUser->isAdmin());
        $this->assertFalse($clientUser->isAdmin());
        $this->assertFalse($freelancerUser->isAdmin());

        $this->assertFalse($adminUser->isClient());
        $this->assertTrue($clientUser->isClient());
        $this->assertFalse($freelancerUser->isClient());

        $this->assertFalse($adminUser->isFreelancer());
        $this->assertFalse($clientUser->isFreelancer());
        $this->assertTrue($freelancerUser->isFreelancer());
    }
}
