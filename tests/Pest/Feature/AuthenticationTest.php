<?php

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('login screen can be rendered', function () {
    $response = $this->get('/login');
    $response->assertStatus(200);
});

test('users can authenticate using the login screen', function () {
    $user = User::factory()->create();
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);
    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('users cannot authenticate with invalid password', function () {
    $user = User::factory()->create();
    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ]);
    $this->assertGuest();
});

test('users can logout', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post('/logout');
    $this->assertGuest();
    $response->assertRedirect('/login');
});

test('users can register with valid data', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'client'
    ]);
    $this->assertAuthenticated();
    $response->assertRedirect(RouteServiceProvider::HOME);
});

test('users cannot register with invalid data', function () {
    $response = $this->post('/register', [
        'name' => '',
        'email' => 'invalid-email',
        'password' => 'pwd',
        'password_confirmation' => 'different-password',
        'role' => 'invalid-role'
    ]);
    $response->assertSessionHasErrors(['name', 'email', 'password']);
    $this->assertGuest();
});

test('admin users can access admin dashboard', function () {
    // Create an admin user and authenticate them using the 'admin' guard
    $admin = User::factory()->admin()->create();
    $response = $this->actingAs($admin, 'admin')->get('/admin/dashboard');
    $response->assertStatus(200);
});

test('non-admin users cannot access admin dashboard', function () {
    $user = User::factory()->create(); // A regular user (non-admin)
    $response = $this->actingAs($user, 'web')->get('/admin/dashboard');

    // Expect a redirect to the admin login page because the user is not authenticated with the 'admin' guard
    $response->assertStatus(302);
    $response->assertRedirect('/login');
});

test('freelancer users can access freelancer dashboard', function () {
    $freelancer = User::factory()->freelancer()->create();
    $response = $this->actingAs($freelancer)->get('/freelancer/dashboard');
    $response->assertStatus(200);
});

test('non-freelancer users cannot access freelancer dashboard', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/freelancer/dashboard');
    $response->assertStatus(403);
});

test('client users can access client dashboard', function () {
    $client = User::factory()->client()->create();
    $response = $this->actingAs($client)->get('/client/dashboard');
    $response->assertStatus(200);
});

test('non-client users cannot access client dashboard', function () {
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/client/dashboard');
    $response->assertStatus(403);
});
