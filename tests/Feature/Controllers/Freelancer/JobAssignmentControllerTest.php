<?php

namespace Tests\Feature\Controllers\Freelancer;

use App\Models\JobAssignment;

use App\Models\User;

use App\Models\Job;

use App\Models\TimeLog;

use Illuminate\Foundation\Testing\RefreshDatabase;


use Tests\TestCase;
class JobAssignmentControllerTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function unauthenticated_users_cannot_access_freelancer_assignment_routes()
    {
        $this->get(route('freelancer.assignments.index'))->assertRedirect(route('login'));

        $assignment = JobAssignment::factory()->create();
        $this->get(route('freelancer.assignments.show', $assignment))->assertRedirect(route('login'));
    }

    #[Test]
    public function non_freelancer_users_cannot_access_freelancer_assignment_routes()
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)
            ->get(route('freelancer.assignments.index'))
            ->assertStatus(403);

        $assignment = JobAssignment::factory()->create();
        $this->actingAs($client)
            ->get(route('freelancer.assignments.show', $assignment))
            ->assertStatus(403);
    }

    #[Test]
    public function freelancer_can_view_their_assignments()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $admin = User::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create();

        $assignment = JobAssignment::factory()->create([
            'job_id' => $job->id,
            'freelancer_id' => $freelancer->id,
            'assigned_by_admin_id' => $admin->id,
            'status' => 'accepted'
        ]);

        $this->actingAs($freelancer)
            ->get(route('freelancer.assignments.index'))
            ->assertStatus(200)
            ->assertSee($job->title);
    }

    #[Test]
    public function freelancer_can_view_their_assignment_details()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $admin = User::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create();

        $assignment = JobAssignment::factory()->create([
            'job_id' => $job->id,
            'freelancer_id' => $freelancer->id,
            'assigned_by_admin_id' => $admin->id,
            'status' => 'accepted'
        ]);

        // Create some time logs for this assignment
        $timeLog = TimeLog::factory()->create([
            'job_assignment_id' => $assignment->id,
            'freelancer_id' => $freelancer->id,
            'task_description' => 'Working on frontend implementation'
        ]);

        $this->actingAs($freelancer)
            ->get(route('freelancer.assignments.show', $assignment))
            ->assertStatus(200)
            ->assertSee($job->title)
            ->assertSee('Working on frontend implementation');
    }

    #[Test]
    public function freelancer_cannot_view_other_freelancers_assignments()
    {
        $freelancer1 = User::factory()->create(['role' => 'freelancer']);
        $freelancer2 = User::factory()->create(['role' => 'freelancer']);
        $admin = User::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create();

        $assignment = JobAssignment::factory()->create([
            'job_id' => $job->id,
            'freelancer_id' => $freelancer2->id,
            'assigned_by_admin_id' => $admin->id,
            'status' => 'accepted'
        ]);

        $this->actingAs($freelancer1)
            ->get(route('freelancer.assignments.show', $assignment))
            ->assertStatus(403);
    }
}
