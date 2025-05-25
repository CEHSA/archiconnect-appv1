<?php

use App\Models\User;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name'                  => 'Test User',
        'email'                 => 'test@example.com',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'role'                  => User::ROLE_CLIENT,
        'company_name'          => 'Test Company',
        'industry'              => 'Technology',
    ]);

    $response->assertRedirect();
    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'name'  => 'Test User',
        'email' => 'test@example.com',
        'role'  => User::ROLE_CLIENT,
    ]);
});

test('new users cannot register with invalid data', function () {
    $response = $this->post('/register', [
        'name'                  => '',
        'email'                 => 'not-an-email',
        'password'              => 'pwd',
        'password_confirmation' => 'different',
        'role'                  => 'invalid-role',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'password', 'role']);
    $this->assertGuest();
});
