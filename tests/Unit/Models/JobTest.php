<?php

namespace Tests\Unit\Models;

use App\Models\Job;

use App\Models\User;

use App\Models\Proposal;

use Illuminate\Foundation\Testing\RefreshDatabase;


use Tests\TestCase;
class JobTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function it_has_correct_fillable_attributes()
    {
        $job = new Job();
        
        $this->assertEquals([
            'user_id',
            'title',
            'description',
            'budget',
            'skills_required',
            'status',
            'hourly_rate',
            'not_to_exceed_budget',
            'created_by_admin_id',
        ], $job->getFillable());
    }

    #[Test]
    public function it_belongs_to_a_user()
    {
        $user = User::factory()->create();
        $job = Job::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $job->user);
        $this->assertEquals($user->id, $job->user->id);
    }

    #[Test]
    public function it_has_many_proposals()
    {
        $job = Job::factory()->create();
        $proposal = Proposal::factory()->create(['job_id' => $job->id]);

        $this->assertInstanceOf(Proposal::class, $job->proposals->first());
        $this->assertEquals($proposal->id, $job->proposals->first()->id);
    }

    #[Test]
    public function it_belongs_to_admin_who_created_it()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create(['created_by_admin_id' => $admin->id]);

        $this->assertInstanceOf(User::class, $job->createdByAdmin);
        $this->assertEquals($admin->id, $job->createdByAdmin->id);
    }

    #[Test]
    public function it_can_be_created_with_valid_data()
    {
        $user = User::factory()->create();
        
        $jobData = [
            'user_id' => $user->id,
            'title' => 'Test Job',
            'description' => 'This is a test job description',
            'budget' => 1000,
            'skills_required' => 'PHP, Laravel',
            'status' => 'open',
        ];

        $job = Job::create($jobData);

        $this->assertInstanceOf(Job::class, $job);
        $this->assertDatabaseHas('jobs', $jobData);
    }
}
