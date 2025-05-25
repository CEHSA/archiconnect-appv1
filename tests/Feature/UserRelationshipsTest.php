<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Job;
use App\Models\FreelancerProfile;
use App\Models\Proposal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRelationshipsTest extends TestCase
{
    use RefreshDatabase;

    public function test_client_user_can_have_many_jobs()
    {
        $client = User::create([
            'name' => 'Test Client',
            'email' => 'client@test.com',
            'password' => 'password',
            'role' => 'client'
        ]);

        $job1 = Job::create([
            'title' => 'Test Job 1',
            'description' => 'Description 1',
            'client_id' => $client->id,
            'budget' => 1000,
            'skills_required' => json_encode(['php']),
            'status' => 'open',
            'hourly_rate' => 50,
            'not_to_exceed_budget' => 2000
        ]);

        $job2 = Job::create([
            'title' => 'Test Job 2',
            'description' => 'Description 2',
            'client_id' => $client->id,
            'budget' => 1500,
            'skills_required' => json_encode(['javascript']),
            'status' => 'open',
            'hourly_rate' => 60,
            'not_to_exceed_budget' => 3000
        ]);

        $this->assertCount(2, $client->jobs);
        $this->assertTrue($client->jobs->contains($job1));
        $this->assertTrue($client->jobs->contains($job2));
    }

    public function test_freelancer_user_can_have_freelancer_profile()
    {
        $freelancer = User::create([
            'name' => 'Test Freelancer',
            'email' => 'freelancer@test.com',
            'password' => 'password',
            'role' => 'freelancer'
        ]);

        $profile = FreelancerProfile::create([
            'user_id' => $freelancer->id,
            'skills' => json_encode(['php', 'laravel']),
            'bio' => 'Test bio'
        ]);

        $this->assertNotNull($freelancer->freelancerProfile);
        $this->assertEquals($profile->id, $freelancer->freelancerProfile->id);
    }

    public function test_freelancer_user_can_have_many_proposals()
    {
        $client = User::create([
            'name' => 'Test Client',
            'email' => 'client2@test.com',
            'password' => 'password',
            'role' => 'client'
        ]);

        $freelancer = User::create([
            'name' => 'Test Freelancer',
            'email' => 'freelancer2@test.com',
            'password' => 'password',
            'role' => 'freelancer'
        ]);

        $job1 = Job::create([
            'title' => 'Test Job 1',
            'description' => 'Description 1',
            'client_id' => $client->id,
            'budget' => 1000,
            'skills_required' => json_encode(['php']),
            'status' => 'open',
            'hourly_rate' => 50,
            'not_to_exceed_budget' => 2000
        ]);

        $job2 = Job::create([
            'title' => 'Test Job 2',
            'description' => 'Description 2',
            'client_id' => $client->id,
            'budget' => 1500,
            'skills_required' => json_encode(['javascript']),
            'status' => 'open',
            'hourly_rate' => 60,
            'not_to_exceed_budget' => 3000
        ]);

        $proposal1 = Proposal::create([
            'user_id' => $freelancer->id,
            'job_id' => $job1->id,
            'proposed_budget' => 900,
            'cover_letter' => 'Cover letter for proposal 1'
        ]);

        $proposal2 = Proposal::create([
            'user_id' => $freelancer->id,
            'job_id' => $job2->id,
            'proposed_budget' => 950,
            'cover_letter' => 'Cover letter for proposal 2'
        ]);

        $this->assertCount(2, $freelancer->proposals);
        $this->assertTrue($freelancer->proposals->contains($proposal1));
        $this->assertTrue($freelancer->proposals->contains($proposal2));
    }
}
