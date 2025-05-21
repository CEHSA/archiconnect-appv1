use Tests\TestCase;
use App\Models\User;
use App\Models\Job;
use App\Models\Dispute;
use App\Models\JobAssignment;
use App\Events\DisputeCreated;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use RefreshDatabase;

<?php

namespace Tests\Unit\Http\Controllers;


class DisputeControllerTest extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
    }

    public function test_client_can_create_dispute()
    {
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $job = Job::factory()->create(['client_id' => $client->id]);
        $jobAssignment = JobAssignment::factory()->create([
            'job_id' => $job->id,
            'freelancer_id' => $freelancer->id
        ]);

        $this->actingAs($client);

        $response = $this->get(route('disputes.create', $jobAssignment));
        $response->assertStatus(200);
        $response->assertViewIs('disputes.create');
    }

    public function test_unauthorized_user_cannot_create_dispute()
    {
        $unauthorized = User::factory()->create();
        $job = Job::factory()->create();
        $jobAssignment = JobAssignment::factory()->create(['job_id' => $job->id]);

        $this->actingAs($unauthorized);

        $response = $this->get(route('disputes.create', $jobAssignment));
        $response->assertStatus(403);
    }

    public function test_store_dispute_with_evidence()
    {
        Event::fake();

        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $job = Job::factory()->create(['client_id' => $client->id]);
        $jobAssignment = JobAssignment::factory()->create([
            'job_id' => $job->id,
            'freelancer_id' => $freelancer->id
        ]);

        $this->actingAs($client);

        $file = UploadedFile::fake()->create('evidence.pdf', 1000);

        $response = $this->post(route('disputes.store', $jobAssignment), [
            'reason' => 'Test dispute reason',
            'evidence' => $file
        ]);

        $response->assertRedirect(route('client.dashboard'));
        $this->assertDatabaseHas('disputes', [
            'job_assignment_id' => $jobAssignment->id,
            'reporter_id' => $client->id,
            'reported_id' => $freelancer->id,
            'reason' => 'Test dispute reason',
            'status' => 'open'
        ]);

        Storage::disk('private')->assertExists('dispute_evidence/' . $file->hashName());
        Event::assertDispatched(DisputeCreated::class);
    }

    public function test_freelancer_can_view_own_disputes()
    {
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $disputes = Dispute::factory(3)->create([
            'reporter_id' => $freelancer->id
        ]);

        $this->actingAs($freelancer);

        $response = $this->get(route('freelancer.disputes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('freelancer.disputes.index');
        $response->assertViewHas('disputes');
    }

    public function test_user_can_only_view_disputes_they_are_involved_in()
    {
        $user = User::factory()->create()->assignRole('client');
        $dispute = Dispute::factory()->create();

        $this->actingAs($user);

        $response = $this->get(route('disputes.show', $dispute));
        $response->assertStatus(403);
    }
}
