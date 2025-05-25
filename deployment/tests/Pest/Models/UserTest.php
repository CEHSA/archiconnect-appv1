<?php

use App\Models\FreelancerProfile;
use App\Models\Job;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

uses(RefreshDatabase::class, WithFaker::class);

test('user has correct fillable attributes', function () {
    $user = new User();
    expect($user->getFillable())->toContain('name', 'email', 'password', 'role');
});

test('user has correct hidden attributes', function () {
    $user = new User();
    expect($user->getHidden())->toContain('password', 'remember_token');
});

test('user has correct casts', function () {
    $user = new User();
    expect($user->getCasts())
        ->toHaveKey('email_verified_at')
        ->toHaveKey('password');
});

test('client user can have many jobs', function () {
    $client = User::factory()->client()->create();
    $job1   = Job::factory()->create(['client_id' => $client->id]);
    $job2   = Job::factory()->create(['client_id' => $client->id]);

    expect($client->jobs)->toHaveCount(2);
    expect($client->jobs->contains($job1))->toBeTrue();
    expect($client->jobs->contains($job2))->toBeTrue();
});

test('freelancer user can have a freelancer profile', function () {
    $freelancer = User::factory()->freelancer()->create();
    $profile    = FreelancerProfile::factory()->create(['user_id' => $freelancer->id]);

    expect($freelancer->freelancerProfile)->toBeInstanceOf(FreelancerProfile::class);
    expect($freelancer->freelancerProfile->id)->toBe($profile->id);
});

test('freelancer user can have many proposals', function () {
    $freelancer = User::factory()->freelancer()->create();
    $proposal1  = Proposal::factory()->create(['freelancer_id' => $freelancer->id]);
    $proposal2  = Proposal::factory()->create(['freelancer_id' => $freelancer->id]);

    expect($freelancer->proposals)->toHaveCount(2);
    expect($freelancer->proposals->contains($proposal1))->toBeTrue();
    expect($freelancer->proposals->contains($proposal2))->toBeTrue();
});

test('user can be created with valid data', function () {
    $userData = [
        'name'     => 'Test User',
        'email'    => 'test@example.com',
        'password' => 'password123',
        'role'     => User::ROLE_CLIENT,
    ];

    $user = User::create($userData);
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe($userData['name'])
        ->and($user->email)->toBe($userData['email'])
        ->and($user->role)->toBe($userData['role']);
});

test('user can check if they are an admin', function () {
    $admin  = User::factory()->admin()->create();
    $client = User::factory()->client()->create();

    expect($admin->isAdmin())->toBeTrue()
        ->and($client->isAdmin())->toBeFalse();
});

test('user can check if they are a freelancer', function () {
    $freelancer = User::factory()->freelancer()->create();
    $client     = User::factory()->client()->create();

    expect($freelancer->isFreelancer())->toBeTrue()
        ->and($client->isFreelancer())->toBeFalse();
});

test('user can check if they are a client', function () {
    $client = User::factory()->client()->create();
    $admin  = User::factory()->admin()->create();

    expect($client->isClient())->toBeTrue()
        ->and($admin->isClient())->toBeFalse();
});
