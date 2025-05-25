<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_has_correct_fillable_attributes()
    {
        $user = new User();
        $this->assertContains('name', $user->getFillable());
        $this->assertContains('email', $user->getFillable());
        $this->assertContains('password', $user->getFillable());
        $this->assertContains('role', $user->getFillable());
    }

    public function test_user_has_correct_hidden_attributes()
    {
        $user = new User();
        $this->assertContains('password', $user->getHidden());
        $this->assertContains('remember_token', $user->getHidden());
    }

    public function test_user_has_correct_casts()
    {
        $user = new User();
        $casts = $user->getCasts();
        $this->assertArrayHasKey('email_verified_at', $casts);
        $this->assertArrayHasKey('password', $casts);
    }

    public function test_user_can_check_if_they_are_an_admin()
    {
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'admin'
        ]);

        $client = User::create([
            'name' => 'Client User',
            'email' => 'client@example.com',
            'password' => 'password',
            'role' => 'client'
        ]);

        $this->assertTrue($admin->isAdmin());
        $this->assertFalse($client->isAdmin());
    }

    public function test_user_can_check_if_they_are_a_freelancer()
    {
        $freelancer = User::create([
            'name' => 'Freelancer User',
            'email' => 'freelancer@example.com',
            'password' => 'password',
            'role' => 'freelancer'
        ]);

        $client = User::create([
            'name' => 'Client User 2',
            'email' => 'client2@example.com',
            'password' => 'password',
            'role' => 'client'
        ]);

        $this->assertTrue($freelancer->isFreelancer());
        $this->assertFalse($client->isFreelancer());
    }

    public function test_user_can_check_if_they_are_a_client()
    {
        $client = User::create([
            'name' => 'Client User 3',
            'email' => 'client3@example.com',
            'password' => 'password',
            'role' => 'client'
        ]);

        $admin = User::create([
            'name' => 'Admin User 2',
            'email' => 'admin2@example.com',
            'password' => 'password',
            'role' => 'admin'
        ]);

        $this->assertTrue($client->isClient());
        $this->assertFalse($admin->isClient());
    }
}
