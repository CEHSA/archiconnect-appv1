<?php

namespace Tests\Feature\Api;

use App\Models\Job;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Laravel\Sanctum\Sanctum;


use Tests\TestCase;
class JobApiTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function it_returns_a_list_of_jobs()
    {
        // Create a client user
        $client = User::factory()->create(['role' => 'client']);

        // Create some jobs
        $jobs = Job::factory()->count(3)->create(['user_id' => $client->id]);

        // Authenticate as the client
        Sanctum::actingAs($client);

        // Make the API request
        $response = $this->getJson('/api/jobs');

        // Assert the response
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'budget',
                        'skills_required',
                        'status',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    #[Test]
    public function it_returns_a_single_job()
    {
        // Create a client user
        $client = User::factory()->create(['role' => 'client']);

        // Create a job
        $job = Job::factory()->create(['user_id' => $client->id]);

        // Authenticate as the client
        Sanctum::actingAs($client);

        // Make the API request
        $response = $this->getJson("/api/jobs/{$job->id}");

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $job->id,
                    'title' => $job->title,
                    'description' => $job->description,
                ]
            ]);
    }

    #[Test]
    public function it_creates_a_new_job()
    {
        // Create a client user
        $client = User::factory()->create(['role' => 'client']);

        // Authenticate as the client
        Sanctum::actingAs($client);

        // Job data
        $jobData = [
            'title' => 'New API Job',
            'description' => 'This is a job created via API',
            'budget' => 1000,
            'skills_required' => 'PHP, Laravel, API',
        ];

        // Make the API request
        $response = $this->postJson('/api/jobs', $jobData);

        // Assert the response
        $response->assertStatus(201)
            ->assertJson([
                'data' => [
                    'title' => 'New API Job',
                    'description' => 'This is a job created via API',
                    'budget' => 1000,
                    'skills_required' => 'PHP, Laravel, API',
                ]
            ]);

        // Assert the job was created in the database
        $this->assertDatabaseHas('jobs', [
            'title' => 'New API Job',
            'user_id' => $client->id,
        ]);
    }

    #[Test]
    public function it_updates_a_job()
    {
        // Create a client user
        $client = User::factory()->create(['role' => 'client']);

        // Create a job
        $job = Job::factory()->create(['user_id' => $client->id]);

        // Authenticate as the client
        Sanctum::actingAs($client);

        // Updated job data
        $updatedData = [
            'title' => 'Updated Job Title',
            'description' => 'Updated job description',
            'budget' => 1500,
        ];

        // Make the API request
        $response = $this->putJson("/api/jobs/{$job->id}", $updatedData);

        // Assert the response
        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $job->id,
                    'title' => 'Updated Job Title',
                    'description' => 'Updated job description',
                    'budget' => 1500,
                ]
            ]);

        // Assert the job was updated in the database
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'title' => 'Updated Job Title',
        ]);
    }

    #[Test]
    public function it_deletes_a_job()
    {
        // Create a client user
        $client = User::factory()->create(['role' => 'client']);

        // Create a job
        $job = Job::factory()->create(['user_id' => $client->id]);

        // Authenticate as the client
        Sanctum::actingAs($client);

        // Make the API request
        $response = $this->deleteJson("/api/jobs/{$job->id}");

        // Assert the response
        $response->assertStatus(204);

        // Assert the job was deleted from the database
        $this->assertDatabaseMissing('jobs', [
            'id' => $job->id,
        ]);
    }
}
