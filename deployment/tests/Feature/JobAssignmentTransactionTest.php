<?php

namespace Tests\Feature;

use App\Models\Job;

use App\Models\JobAssignment;

use App\Models\Proposal;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;

use Illuminate\Support\Facades\DB;


use Tests\TestCase;
class JobAssignmentTransactionTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function accepting_proposal_creates_job_assignment_in_transaction()
    {
        // Create a client
        $client = User::factory()->create(['role' => 'client']);

        // Create an admin
        $admin = User::factory()->create(['role' => 'admin']);

        // Create a job
        $job = Job::factory()->create(['user_id' => $client->id, 'status' => 'open']);

        // Create a freelancer
        $freelancer = User::factory()->create(['role' => 'freelancer']);

        // Create a proposal
        $proposal = Proposal::factory()->create([
            'job_id' => $job->id,
            'user_id' => $freelancer->id,
            'status' => 'pending'
        ]);

        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Accept the proposal
            $proposal->update(['status' => 'accepted']);

            // Create a job assignment
            $assignment = JobAssignment::create([
                'job_id' => $job->id,
                'freelancer_id' => $freelancer->id,
                'assigned_by_admin_id' => $admin->id, // Admin assignment
                'status' => 'accepted',
                'notes' => 'Assigned based on accepted proposal',
            ]);

            // Update the job status
            $job->update(['status' => 'in_progress']);

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        // Assert all changes were saved
        $this->assertDatabaseHas('proposals', [
            'id' => $proposal->id,
            'status' => 'accepted'
        ]);

        $this->assertDatabaseHas('job_assignments', [
            'job_id' => $job->id,
            'freelancer_id' => $freelancer->id,
            'status' => 'accepted'
        ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'status' => 'in_progress'
        ]);
    }
    #[Test]
    public function transaction_rolls_back_on_error()
    {
        // Create a client
        $client = User::factory()->create(['role' => 'client']);

        // Create a job
        $job = Job::factory()->create(['user_id' => $client->id, 'status' => 'open']);

        // Create a freelancer
        $freelancer = User::factory()->create(['role' => 'freelancer']);

        // Create a proposal
        $proposal = Proposal::factory()->create([
            'job_id' => $job->id,
            'user_id' => $freelancer->id,
            'status' => 'pending'
        ]);

        // Begin a database transaction
        DB::beginTransaction();

        try {
            // Accept the proposal
            $proposal->update(['status' => 'accepted']);

            // Try to create an invalid job assignment (missing required field)
            JobAssignment::create([
                'job_id' => $job->id,
                // Missing freelancer_id which should cause an error
                'status' => 'accepted',
            ]);


            // This line should not be reached due to the error above
            $job->update(['status' => 'in_progress']);

            // Commit the transaction
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // We expect an exception, so we'll just catch it and roll back
        }

        // Assert no changes were saved due to rollback
        $this->assertDatabaseHas('proposals', [
            'id' => $proposal->id,
            'status' => 'pending' // Still pending, not accepted
        ]);

        $this->assertDatabaseMissing('job_assignments', [
            'job_id' => $job->id,
            'status' => 'accepted'
        ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'status' => 'open' // Still open, not in_progress
        ]);
    }
}
