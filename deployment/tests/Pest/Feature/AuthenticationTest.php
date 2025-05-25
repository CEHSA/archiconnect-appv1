<?php

use App\Models\User;

test('client dashboard access control', function () {
    // Client can access their dashboard
    $client = User::factory()->client()->create();
    $this->actingAs($client)
        ->get('/client/dashboard')
        ->assertStatus(200);

    // Non-client cannot access client dashboard
    $nonClient = User::factory()->freelancer()->create();
    $this->actingAs($nonClient)
        ->get('/client/dashboard')
        ->assertStatus(403);
});

test('freelancer dashboard access control', function () {
    // Freelancer can access their dashboard
    $freelancer = User::factory()->freelancer()->create();
    $this->actingAs($freelancer)
        ->get('/freelancer/dashboard')
        ->assertStatus(200);

    // Non-freelancer cannot access freelancer dashboard
    $nonFreelancer = User::factory()->client()->create();
    $this->actingAs($nonFreelancer)
        ->get('/freelancer/dashboard')
        ->assertStatus(403);
});

test('admin dashboard access control', function () {
    // Admin can access their dashboard
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin, 'admin')
        ->get('/admin/dashboard')
        ->assertStatus(200);

    // Non-admin cannot access admin dashboard
    $nonAdmin = User::factory()->client()->create();
    $this->actingAs($nonAdmin)
        ->get('/admin/dashboard')
        ->assertStatus(403);
});
