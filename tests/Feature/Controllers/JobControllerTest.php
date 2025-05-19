<?php

namespace Tests\Feature\Controllers;

use App\Models\Job;


use App\Models\User;


use Illuminate\Foundation\Testing\RefreshDatabase;


use Tests\TestCase;
class JobControllerTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function unauthenticated_users_cannot_access_job_routes()
    {
        $this->get(route('client.jobs.index'))->assertRedirect(route('login'));
        $this->get(route('client.jobs.create'))->assertRedirect(route('login'));
        $this->post(route('client.jobs.store'))->assertRedirect(route('login'));
    }
    #[Test]
    public function client_can_view_their_jobs()
    {
        $client = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client->id]);
        
        $this->actingAs($client)
            ->get(route('client.jobs.index'))
            ->assertStatus(200)
            ->assertSee($job->title);
    }
    #[Test]
    public function client_can_create_a_job()
    {
        $client = User::factory()->create(['role' => 'client']);
        
        $this->actingAs($client)
            ->get(route('client.jobs.create'))
            ->assertStatus(200);
            
        $jobData = [
            'title' => 'New Test Job',
            'description' => 'This is a test job description',
            'budget' => 1000,
            'skills_required' => 'PHP, Laravel',
        ];
        
        $this->actingAs($client)
            ->post(route('client.jobs.store'), $jobData)
            ->assertRedirect(route('client.jobs.index'));
            
        $this->assertDatabaseHas('jobs', array_merge($jobData, ['user_id' => $client->id]));
    }
    #[Test]
    public function client_can_view_their_job_details()
    {
        $client = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client->id]);
        
        $this->actingAs($client)
            ->get(route('client.jobs.show', $job))
            ->assertStatus(200)
            ->assertSee($job->title)
            ->assertSee($job->description);
    }
    #[Test]
    public function client_cannot_view_other_clients_jobs()
    {
        $client1 = User::factory()->create(['role' => 'client']);
        $client2 = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client2->id]);
        
        $this->actingAs($client1)
            ->get(route('client.jobs.show', $job))
            ->assertStatus(403);
    }
    #[Test]
    public function client_can_update_their_job()
    {
        $client = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client->id]);
        
        $updatedData = [
            'title' => 'Updated Job Title',
            'description' => 'Updated job description',
            'budget' => 1500,
            'skills_required' => 'PHP, Laravel, Vue.js',
            'status' => 'open',
        ];
        
        $this->actingAs($client)
            ->put(route('client.jobs.update', $job), $updatedData)
            ->assertRedirect(route('client.jobs.index'));
            
        $this->assertDatabaseHas('jobs', array_merge($updatedData, ['id' => $job->id]));
    }
    #[Test]
    public function client_can_delete_their_job()
    {
        $client = User::factory()->create(['role' => 'client']);
        $job = Job::factory()->create(['user_id' => $client->id]);
        
        $this->actingAs($client)
            ->delete(route('client.jobs.destroy', $job))
            ->assertRedirect(route('client.jobs.index'));
            
        $this->assertDatabaseMissing('jobs', ['id' => $job->id]);
    }
    #[Test]
    public function freelancer_can_browse_available_jobs()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $job = Job::factory()->create(['status' => 'open']);
        
        $this->actingAs($freelancer)
            ->get(route('freelancer.jobs.browse'))
            ->assertStatus(200)
            ->assertSee($job->title);
    }
}
