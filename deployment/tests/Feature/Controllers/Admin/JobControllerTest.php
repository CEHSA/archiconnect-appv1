<?php

namespace Tests\Feature\Controllers\Admin;

use App\Models\Job;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;


use Tests\TestCase;
class JobControllerTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function unauthenticated_users_cannot_access_admin_job_routes()
    {
        $this->get(route('admin.jobs.index'))->assertRedirect(route('login'));
        $this->get(route('admin.jobs.create'))->assertRedirect(route('login'));
        $this->post(route('admin.jobs.store'))->assertRedirect(route('login'));
    }

    #[Test]
    public function non_admin_users_cannot_access_admin_job_routes()
    {
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($client)
            ->get(route('admin.jobs.index'))
            ->assertStatus(403);

        $this->actingAs($client)
            ->get(route('admin.jobs.create'))
            ->assertStatus(403);
    }

    #[Test]
    public function admin_can_view_all_jobs()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.jobs.index'))
            ->assertStatus(200)
            ->assertSee($job->title);
    }

    #[Test]
    public function admin_can_create_a_job()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = User::factory()->create(['role' => 'client']);

        $this->actingAs($admin)
            ->get(route('admin.jobs.create'))
            ->assertStatus(200);

        $jobData = [
            'title' => 'Admin Created Job',
            'user_id' => $client->id,
            'description' => 'This is a job created by an admin',
            'budget' => 2000,
            'hourly_rate' => 50,
            'not_to_exceed_budget' => 3000,
            'skills_required' => 'PHP, Laravel, Vue.js',
            'status' => 'open',
        ];

        $this->actingAs($admin)
            ->post(route('admin.jobs.store'), $jobData)
            ->assertRedirect(route('admin.jobs.index'));

        $this->assertDatabaseHas('jobs', array_merge($jobData, ['created_by_admin_id' => $admin->id]));
    }

    #[Test]
    public function admin_can_view_job_details()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.jobs.show', $job))
            ->assertStatus(200)
            ->assertSee($job->title)
            ->assertSee($job->description);
    }

    #[Test]
    public function admin_can_edit_a_job()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create();

        $this->actingAs($admin)
            ->get(route('admin.jobs.edit', $job))
            ->assertStatus(200)
            ->assertSee($job->title);
    }

    #[Test]
    public function admin_can_update_a_job()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create();
        $client = User::factory()->create(['role' => 'client']);

        $updatedData = [
            'title' => 'Updated Job Title',
            'user_id' => $client->id,
            'description' => 'Updated job description',
            'budget' => 2500,
            'hourly_rate' => 60,
            'not_to_exceed_budget' => 3500,
            'skills_required' => 'PHP, Laravel, Vue.js, React',
            'status' => 'in_progress',
        ];

        $this->actingAs($admin)
            ->put(route('admin.jobs.update', $job), $updatedData)
            ->assertRedirect(route('admin.jobs.index'));

        $this->assertDatabaseHas('jobs', array_merge($updatedData, ['id' => $job->id]));
    }

    #[Test]
    public function admin_can_delete_a_job()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $job = Job::factory()->create();

        $this->actingAs($admin)
            ->delete(route('admin.jobs.destroy', $job))
            ->assertRedirect(route('admin.jobs.index'));

        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }
}
