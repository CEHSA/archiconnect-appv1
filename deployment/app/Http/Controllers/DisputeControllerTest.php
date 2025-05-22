<?php

namespace Tests\Unit\Http\Controllers;

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

class DisputeControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private'); // Fake the 'private' disk for evidence uploads
        Event::fake(); // Fake events to prevent actual event dispatching like notifications
    }

    // --- Test cases for create() method ---

    public function test_client_can_access_create_dispute_form_for_their_job_assignment()
    {
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $job = Job::factory()->create(['client_id' => $client->id]);
        $jobAssignment = JobAssignment::factory()->create(['job_id' => $job->id, 'freelancer_id' => $freelancer->id]);

        $this->actingAs($client);
        $response = $this->get(route('disputes.create', $jobAssignment));

        $response->assertStatus(200);
        $response->assertViewIs('disputes.create');
        $response->assertViewHas('jobAssignment', $jobAssignment);
    }

    public function test_freelancer_can_access_create_dispute_form_for_their_job_assignment()
    {
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $job = Job::factory()->create(['client_id' => $client->id]);
        $jobAssignment = JobAssignment::factory()->create(['job_id' => $job->id, 'freelancer_id' => $freelancer->id]);

        $this->actingAs($freelancer);
        $response = $this->get(route('disputes.create', $jobAssignment));

        $response->assertStatus(200);
        $response->assertViewIs('disputes.create');
    }

    public function test_user_cannot_access_create_dispute_form_if_not_part_of_job_assignment()
    {
        $otherUser = User::factory()->create(); // User not client nor freelancer for the job
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $job = Job::factory()->create(['client_id' => $client->id]);
        $jobAssignment = JobAssignment::factory()->create(['job_id' => $job->id, 'freelancer_id' => $freelancer->id]);

        $this->actingAs($otherUser);
        $response = $this->get(route('disputes.create', $jobAssignment));

        $response->assertStatus(403);
    }

    public function test_admin_cannot_access_create_dispute_form()
    {
        $admin = User::factory()->create()->assignRole('admin');
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $job = Job::factory()->create(['client_id' => $client->id]);
        $jobAssignment = JobAssignment::factory()->create(['job_id' => $job->id, 'freelancer_id' => $freelancer->id]);

        $this->actingAs($admin);
        $response = $this->get(route('disputes.create', $jobAssignment));
        // Assuming admins are not supposed to create disputes this way,
        // and the authorization logic or middleware would block them.
        // The current controller logic checks if user is client or freelancer for the job.
        $response->assertStatus(403);
    }

    public function test_user_cannot_create_dispute_if_one_is_already_open_for_same_assignment()
    {
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $job = Job::factory()->create(['client_id' => $client->id]);
        $jobAssignment = JobAssignment::factory()->create(['job_id' => $job->id, 'freelancer_id' => $freelancer->id]);

        Dispute::factory()->create([
            'job_assignment_id' => $jobAssignment->id,
            'reporter_id' => $client->id,
            'status' => 'open'
        ]);

        $this->actingAs($client);
        $response = $this->get(route('disputes.create', $jobAssignment));

        $response->assertRedirect(); // Or specific route if defined
        $response->assertSessionHas('error', 'You already have an open dispute for this job assignment.');
    }

    // --- Test cases for store() method ---

    public function test_client_can_store_dispute_with_evidence()
    {
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $job = Job::factory()->create(['client_id' => $client->id]);
        $jobAssignment = JobAssignment::factory()->create(['job_id' => $job->id, 'freelancer_id' => $freelancer->id]);
        $file = UploadedFile::fake()->create('evidence.pdf', 1024); // 1MB PDF

        $this->actingAs($client);
        $response = $this->post(route('disputes.store', $jobAssignment), [
            'reason' => 'Client test dispute reason with evidence.',
            'evidence' => $file,
        ]);

        $response->assertRedirect(route('client.dashboard'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('disputes', [
            'job_assignment_id' => $jobAssignment->id,
            'reporter_id' => $client->id,
            'reported_id' => $freelancer->id,
            'reason' => 'Client test dispute reason with evidence.',
            'status' => 'open',
        ]);
        $dispute = Dispute::first();
        Storage::disk('private')->assertExists($dispute->evidence_path);
        Event::assertDispatched(DisputeCreated::class);
    }

    public function test_client_can_store_dispute_without_evidence()
    {
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $job = Job::factory()->create(['client_id' => $client->id]);
        $jobAssignment = JobAssignment::factory()->create(['job_id' => $job->id, 'freelancer_id' => $freelancer->id]);

        $this->actingAs($client);
        $response = $this->post(route('disputes.store', $jobAssignment), [
            'reason' => 'Client test dispute reason no evidence.',
        ]);

        $response->assertRedirect(route('client.dashboard'));
        $this->assertDatabaseHas('disputes', [
            'reporter_id' => $client->id,
            'reason' => 'Client test dispute reason no evidence.',
            'evidence_path' => null,
        ]);
        Event::assertDispatched(DisputeCreated::class);
    }

    public function test_freelancer_can_store_dispute_with_evidence()
    {
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $job = Job::factory()->create(['client_id' => $client->id]);
        $jobAssignment = JobAssignment::factory()->create(['job_id' => $job->id, 'freelancer_id' => $freelancer->id]);
        $file = UploadedFile::fake()->create('evidence_freelancer.jpg', 500);

        $this->actingAs($freelancer);
        $response = $this->post(route('disputes.store', $jobAssignment), [
            'reason' => 'Freelancer test dispute reason with evidence.',
            'evidence' => $file,
        ]);

        $response->assertRedirect(route('freelancer.dashboard'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('disputes', [
            'job_assignment_id' => $jobAssignment->id,
            'reporter_id' => $freelancer->id,
            'reported_id' => $client->id, // Reported party is client
            'reason' => 'Freelancer test dispute reason with evidence.',
        ]);
        $dispute = Dispute::where('reporter_id', $freelancer->id)->first();
        Storage::disk('private')->assertExists($dispute->evidence_path);
        Event::assertDispatched(DisputeCreated::class);
    }

    public function test_store_dispute_fails_validation_for_missing_reason()
    {
        $client = User::factory()->create()->assignRole('client');
        $jobAssignment = JobAssignment::factory()->create(['job_id' => Job::factory()->create(['client_id' => $client->id])->id]);

        $this->actingAs($client);
        $response = $this->post(route('disputes.store', $jobAssignment), ['evidence' => UploadedFile::fake()->create('evidence.pdf')]);

        $response->assertSessionHasErrors('reason');
    }

    public function test_store_dispute_fails_validation_for_invalid_evidence_type()
    {
        $client = User::factory()->create()->assignRole('client');
        $jobAssignment = JobAssignment::factory()->create(['job_id' => Job::factory()->create(['client_id' => $client->id])->id]);

        $this->actingAs($client);
        $response = $this->post(route('disputes.store', $jobAssignment), [
            'reason' => 'Test reason',
            'evidence' => UploadedFile::fake()->create('evidence.txt', 100), // Invalid type
        ]);
        $response->assertSessionHasErrors('evidence');
    }

    public function test_store_dispute_fails_validation_for_evidence_too_large()
    {
        $client = User::factory()->create()->assignRole('client');
        $jobAssignment = JobAssignment::factory()->create(['job_id' => Job::factory()->create(['client_id' => $client->id])->id]);

        $this->actingAs($client);
        $response = $this->post(route('disputes.store', $jobAssignment), [
            'reason' => 'Test reason',
            'evidence' => UploadedFile::fake()->create('evidence.pdf', 6000), // Max 5MB (5120KB)
        ]);
        $response->assertSessionHasErrors('evidence');
    }

    public function test_admin_cannot_store_dispute()
    {
        $admin = User::factory()->create()->assignRole('admin');
        $jobAssignment = JobAssignment::factory()->create();

        $this->actingAs($admin);
        $response = $this->post(route('disputes.store', $jobAssignment), ['reason' => 'Admin trying to store']);
        $response->assertStatus(403);
    }

    // --- Test cases for freelancerIndex() method ---

    public function test_freelancer_can_view_their_disputes_index()
    {
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $client = User::factory()->create()->assignRole('client');

        // Dispute reported by freelancer
        Dispute::factory()->create([
            'reporter_id' => $freelancer->id,
            'reported_id' => $client->id,
            'job_assignment_id' => JobAssignment::factory()->create(['freelancer_id' => $freelancer->id, 'job_id' => Job::factory()->create(['client_id' => $client->id])->id])->id
        ]);
        // Dispute where freelancer is reported
        Dispute::factory()->create([
            'reporter_id' => $client->id,
            'reported_id' => $freelancer->id,
            'job_assignment_id' => JobAssignment::factory()->create(['freelancer_id' => $freelancer->id, 'job_id' => Job::factory()->create(['client_id' => $client->id])->id])->id
        ]);
        // Unrelated dispute
        Dispute::factory()->create();


        $this->actingAs($freelancer);
        $response = $this->get(route('freelancer.disputes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('freelancer.disputes.index');
        $response->assertViewHas('disputes', function ($disputes) use ($freelancer) {
            return $disputes->every(function ($dispute) use ($freelancer) {
                return $dispute->reporter_id === $freelancer->id || $dispute->reported_id === $freelancer->id;
            }) && $disputes->count() === 2;
        });
    }

    public function test_client_cannot_access_freelancer_disputes_index()
    {
        $client = User::factory()->create()->assignRole('client');
        $this->actingAs($client);
        $response = $this->get(route('freelancer.disputes.index'));
        // This depends on middleware, assuming it redirects or gives 403
        // If specific 'freelancer' middleware is applied to the route.
        // For now, let's assume a generic auth middleware might allow if logged in,
        // but the view/controller logic is for freelancers.
        // A more robust test would check for specific middleware behavior.
        // If no specific role middleware, it might pass then fail on view or data.
        // Let's assume a 403 if a role middleware is in place.
        $response->assertStatus(403); // Or assertRedirect if middleware redirects non-freelancers
    }

    public function test_admin_cannot_access_freelancer_disputes_index()
    {
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin);
        $response = $this->get(route('freelancer.disputes.index'));
        $response->assertStatus(403); // Or assertRedirect
    }

    // --- Test cases for clientIndex() method ---

    public function test_client_can_view_their_disputes_index()
    {
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');

        // Dispute reported by client
        Dispute::factory()->create([
            'reporter_id' => $client->id,
            'reported_id' => $freelancer->id,
            'job_assignment_id' => JobAssignment::factory()->create(['freelancer_id' => $freelancer->id, 'job_id' => Job::factory()->create(['client_id' => $client->id])->id])->id
        ]);
        // Dispute where client is reported
        Dispute::factory()->create([
            'reporter_id' => $freelancer->id,
            'reported_id' => $client->id,
            'job_assignment_id' => JobAssignment::factory()->create(['freelancer_id' => $freelancer->id, 'job_id' => Job::factory()->create(['client_id' => $client->id])->id])->id
        ]);
        // Unrelated dispute
        Dispute::factory()->create();

        $this->actingAs($client);
        $response = $this->get(route('client.disputes.index'));

        $response->assertStatus(200);
        $response->assertViewIs('client.disputes.index'); // Assumes view exists
        $response->assertViewHas('disputes', function ($disputes) use ($client) {
            return $disputes->every(function ($dispute) use ($client) {
                return $dispute->reporter_id === $client->id || $dispute->reported_id === $client->id;
            }) && $disputes->count() === 2;
        });
    }

    public function test_freelancer_cannot_access_client_disputes_index()
    {
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $this->actingAs($freelancer);
        $response = $this->get(route('client.disputes.index'));
        $response->assertStatus(403); // Or assertRedirect
    }

    public function test_admin_cannot_access_client_disputes_index()
    {
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin);
        $response = $this->get(route('client.disputes.index'));
        $response->assertStatus(403); // Or assertRedirect
    }

    // --- Test cases for show() method ---

    public function test_client_can_view_dispute_they_are_involved_in_as_reporter()
    {
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $dispute = Dispute::factory()->create([
            'reporter_id' => $client->id,
            'reported_id' => $freelancer->id,
            'job_assignment_id' => JobAssignment::factory()->create(['freelancer_id' => $freelancer->id, 'job_id' => Job::factory()->create(['client_id' => $client->id])->id])->id
        ]);

        $this->actingAs($client);
        $response = $this->get(route('disputes.show', $dispute));

        $response->assertStatus(200);
        $response->assertViewIs('client.disputes.show'); // Assumes view exists
        $response->assertViewHas('dispute', $dispute);
    }

    public function test_client_can_view_dispute_they_are_involved_in_as_reported()
    {
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $dispute = Dispute::factory()->create([
            'reporter_id' => $freelancer->id,
            'reported_id' => $client->id,
            'job_assignment_id' => JobAssignment::factory()->create(['freelancer_id' => $freelancer->id, 'job_id' => Job::factory()->create(['client_id' => $client->id])->id])->id
        ]);

        $this->actingAs($client);
        $response = $this->get(route('disputes.show', $dispute));

        $response->assertStatus(200);
        $response->assertViewIs('client.disputes.show');
    }

    public function test_freelancer_can_view_dispute_they_are_involved_in_as_reporter()
    {
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $dispute = Dispute::factory()->create([
            'reporter_id' => $freelancer->id,
            'reported_id' => $client->id,
            'job_assignment_id' => JobAssignment::factory()->create(['freelancer_id' => $freelancer->id, 'job_id' => Job::factory()->create(['client_id' => $client->id])->id])->id
        ]);

        $this->actingAs($freelancer);
        $response = $this->get(route('disputes.show', $dispute));

        $response->assertStatus(200);
        $response->assertViewIs('freelancer.disputes.show'); // Assumes view exists
        $response->assertViewHas('dispute', $dispute);
    }

    public function test_freelancer_can_view_dispute_they_are_involved_in_as_reported()
    {
        $client = User::factory()->create()->assignRole('client');
        $freelancer = User::factory()->create()->assignRole('freelancer');
        $dispute = Dispute::factory()->create([
            'reporter_id' => $client->id,
            'reported_id' => $freelancer->id,
            'job_assignment_id' => JobAssignment::factory()->create(['freelancer_id' => $freelancer->id, 'job_id' => Job::factory()->create(['client_id' => $client->id])->id])->id
        ]);

        $this->actingAs($freelancer);
        $response = $this->get(route('disputes.show', $dispute));

        $response->assertStatus(200);
        $response->assertViewIs('freelancer.disputes.show');
    }

    public function test_user_cannot_view_dispute_they_are_not_involved_in()
    {
        $user = User::factory()->create()->assignRole('client'); // Could be any role not involved
        $dispute = Dispute::factory()->create(); // Belongs to other users

        $this->actingAs($user);
        $response = $this->get(route('disputes.show', $dispute));

        $response->assertStatus(403);
    }

    public function test_admin_cannot_view_dispute_via_this_show_method()
    {
        $admin = User::factory()->create()->assignRole('admin');
        $dispute = Dispute::factory()->create();

        $this->actingAs($admin);
        $response = $this->get(route('disputes.show', $dispute));
        // Controller logic explicitly checks for client/freelancer role for this show method
        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_is_redirected_from_dispute_routes()
    {
        $jobAssignment = JobAssignment::factory()->create();
        $dispute = Dispute::factory()->create();

        $this->get(route('disputes.create', $jobAssignment))->assertRedirect(route('login'));
        $this->post(route('disputes.store', $jobAssignment))->assertRedirect(route('login'));
        $this->get(route('freelancer.disputes.index'))->assertRedirect(route('login'));
        $this->get(route('client.disputes.index'))->assertRedirect(route('login'));
        $this->get(route('disputes.show', $dispute))->assertRedirect(route('login'));
    }
}
