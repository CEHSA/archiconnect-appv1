<?php

namespace Tests\Unit\Models;

use App\Models\FreelancerProfile;
use App\Models\Job;
use App\Models\Proposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class UserModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_has_correct_fillable_attributes()
    {
        $user = new User();

        $this->assertContains('name', $user->getFillable());
        $this->assertContains('email', $user->getFillable());
        $this->assertContains('password', $user->getFillable());
        $this->assertContains('role', $user->getFillable());
    }

    #[Test]
    public function user_has_correct_hidden_attributes()
    {
        $user = new User();

        $this->assertContains('password', $user->getHidden());
        $this->assertContains('remember_token', $user->getHidden());
    }

    #[Test]
    public function user_has_correct_casts()
    {
        $user = new User();

        $this->assertArrayHasKey('email_verified_at', $user->getCasts());
        $this->assertArrayHasKey('password', $user->getCasts());
    }

    #[Test]
    public function client_user_can_have_many_jobs()
    {
        // Create a client user
        $client = User::factory()->create(['role' => 'client']);
        
        // Create a job associated with the client
        $job = Job::factory()->create(['user_id' => $client->id]);
        
        // Refresh the client model to load the relationship
        $client->refresh();
        
        // Assert that the client has one job
        $this->assertCount(1, $client->jobs);
        
        // Assert that the job belongs to the client
        $this->assertEquals($job->id, $client->jobs->first()->id);
    }

    #[Test]
    public function freelancer_user_can_have_a_freelancer_profile()
    {
        // Create a freelancer user
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        
        // Create a profile for the freelancer
        $profile = FreelancerProfile::factory()->create(['user_id' => $freelancer->id]);
        
        // Refresh the freelancer model to load the relationship
        $freelancer->refresh();
        
        // Assert that the freelancer has a profile
        $this->assertInstanceOf(FreelancerProfile::class, $freelancer->freelancerProfile);
        
        // Assert that the profile belongs to the freelancer
        $this->assertEquals($profile->id, $freelancer->freelancerProfile->id);
    }

    #[Test]
    public function freelancer_user_can_have_many_proposals()
    {
        // Create a freelancer user
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        
        // Create a job
        $job = Job::factory()->create();
        
        // Create a proposal from the freelancer for the job
        $proposal = Proposal::factory()->create([
            'user_id' => $freelancer->id,
            'job_id' => $job->id
        ]);
        
        // Refresh the freelancer model to load the relationship
        $freelancer->refresh();
        
        // Assert that the freelancer has one proposal
        $this->assertCount(1, $freelancer->proposals);
        
        // Assert that the proposal belongs to the freelancer
        $this->assertEquals($proposal->id, $freelancer->proposals->first()->id);
    }

    #[Test]
    public function user_can_be_created_with_valid_data()
    {
        // Create user data
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ];
        
        // Create the user
        $user = User::create($userData);
        
        // Assert that the user was created correctly
        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals('client', $user->role);
        
        // Assert that the user exists in the database
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'role' => 'client',
        ]);
    }
}
