<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\ClientProfile;
use App\Models\FreelancerProfile;
use App\Models\Job;
use App\Models\JobAssignment;
use App\Models\BriefingRequest;
use App\Models\WorkSubmission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile; // Added for file uploads

class UserRoleInteractionsTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Test that an Admin can assign a job to a Freelancer.
     *
     * @return void
     */
    public function test_admin_can_assign_job_to_freelancer()
    {
        $adminUser = User::factory()->admin()->create();
        $adminProfile = Admin::factory()->create(['user_id' => $adminUser->id]);

        $freelancerUser = User::factory()->freelancer()->create();
        FreelancerProfile::factory()->create(['user_id' => $freelancerUser->id]);

        $clientUser = User::factory()->client()->create();
        ClientProfile::factory()->create(['user_id' => $clientUser->id]);

        $job = Job::factory()->create([
            'user_id' => $clientUser->id, // The user who posted the job (client)
            'client_id' => $clientUser->id,
            'created_by_admin_id' => $adminUser->id,
            'status' => 'pending_assignment',
        ]);

        $response = $this->actingAs($adminUser, 'admin')->postJson(route('admin.jobs.assign', $job->id), [
            'freelancer_id' => $freelancerUser->id,
            'assigned_by_admin_id' => $adminProfile->id,
            'status' => 'assigned',
        ]);

        $response->assertStatus(200); // Or 201 if it's a creation endpoint
        $this->assertDatabaseHas('job_assignments', [
            'job_id' => $job->id,
            'freelancer_id' => $freelancerUser->id,
            'assigned_by_admin_id' => $adminProfile->id,
            'status' => 'assigned',
        ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'assigned_freelancer_id' => $freelancerUser->id,
            'status' => 'assigned',
        ]);
    }

    /**
     * Test that a Client can submit a briefing request.
     *
     * @return void
     */
    public function test_client_can_submit_briefing_request()
    {
        $clientUser = User::factory()->client()->create();
        ClientProfile::factory()->create(['user_id' => $clientUser->id]);

        $briefingData = [
            'project_type' => $this->faker->randomElement(['residential', 'commercial', 'industrial']),
            'description' => $this->faker->paragraph,
            'preferred_datetime' => now()->addDays(7)->format('Y-m-d H:i:s'),
        ];

        $response = $this->actingAs($clientUser, 'web')->postJson(route('client.briefing-requests.store'), $briefingData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('briefing_requests', [
            'client_id' => $clientUser->id,
            'project_type' => $briefingData['project_type'],
            'description' => $briefingData['description'],
        ]);
    }

    /**
     * Test that a Freelancer can submit work for a job.
     *
     * @return void
     */
    public function test_freelancer_can_submit_work()
    {
        $adminUser = User::factory()->admin()->create();
        $adminProfile = Admin::factory()->create(['user_id' => $adminUser->id]);

        $freelancerUser = User::factory()->freelancer()->create();
        FreelancerProfile::factory()->create(['user_id' => $freelancerUser->id]);

        $clientUser = User::factory()->client()->create();
        ClientProfile::factory()->create(['user_id' => $clientUser->id]);

        $job = Job::factory()->create([
            'user_id' => $clientUser->id,
            'client_id' => $clientUser->id,
            'created_by_admin_id' => $adminUser->id,
            'assigned_freelancer_id' => $freelancerUser->id,
            'status' => 'assigned',
        ]);

        $jobAssignment = JobAssignment::factory()->create([
            'job_id' => $job->id,
            'freelancer_id' => $freelancerUser->id,
            'assigned_by_admin_id' => $adminProfile->id,
            'status' => 'assigned',
        ]);

        $workSubmissionData = [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'submission_file' => UploadedFile::fake()->create('document.pdf', 500, 'application/pdf'),
        ];

        $response = $this->actingAs($freelancerUser, 'web')->postJson(route('freelancer.assignments.submissions.store', $jobAssignment->id), $workSubmissionData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('work_submissions', [
            'job_assignment_id' => $jobAssignment->id,
            'freelancer_id' => $freelancerUser->id,
            'title' => $workSubmissionData['title'],
            'status' => WorkSubmission::STATUS_SUBMITTED_FOR_ADMIN_REVIEW, // Ensure status matches default
        ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'status' => 'work_submitted',
        ]);
    }

    /**
     * Test that an Admin can review freelancer work.
     *
     * @return void
     */
    public function test_admin_can_review_freelancer_work()
    {
        $adminUser = User::factory()->admin()->create();
        $adminProfile = Admin::factory()->create(['user_id' => $adminUser->id]);

        $freelancerUser = User::factory()->freelancer()->create();
        FreelancerProfile::factory()->create(['user_id' => $freelancerUser->id]);

        $clientUser = User::factory()->client()->create();
        ClientProfile::factory()->create(['user_id' => $clientUser->id]);

        $job = Job::factory()->create([
            'user_id' => $clientUser->id,
            'client_id' => $clientUser->id,
            'created_by_admin_id' => $adminUser->id,
            'assigned_freelancer_id' => $freelancerUser->id,
            'status' => 'assigned',
        ]);

        $jobAssignment = JobAssignment::factory()->create([
            'job_id' => $job->id,
            'freelancer_id' => $freelancerUser->id,
            'assigned_by_admin_id' => $adminProfile->id,
            'status' => 'assigned',
        ]);

        $workSubmission = WorkSubmission::factory()->create([
            'job_assignment_id' => $jobAssignment->id,
            'freelancer_id' => $freelancerUser->id,
            'status' => WorkSubmission::STATUS_SUBMITTED_FOR_ADMIN_REVIEW,
        ]);

        $response = $this->actingAs($adminUser, 'admin')->postJson(route('admin.work-submissions.review', $workSubmission->id), [
            'status' => WorkSubmission::STATUS_PENDING_CLIENT_REVIEW,
            'admin_remarks' => 'Looks good, forwarding to client.',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('work_submissions', [
            'id' => $workSubmission->id,
            'status' => WorkSubmission::STATUS_PENDING_CLIENT_REVIEW,
            'admin_remarks' => 'Looks good, forwarding to client.',
        ]);

        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'status' => 'admin_reviewed_work',
        ]);
    }

    /**
     * Test that a Client can approve or request revisions on submitted work.
     *
     * @return void
     */
    public function test_client_can_approve_or_request_revisions()
    {
        $adminUser = User::factory()->admin()->create();
        $adminProfile = Admin::factory()->create(['user_id' => $adminUser->id]);

        $freelancerUser = User::factory()->freelancer()->create();
        FreelancerProfile::factory()->create(['user_id' => $freelancerUser->id]);

        $clientUser = User::factory()->client()->create();
        ClientProfile::factory()->create(['user_id' => $clientUser->id]);

        $job = Job::factory()->create([
            'user_id' => $clientUser->id,
            'client_id' => $clientUser->id,
            'created_by_admin_id' => $adminUser->id,
            'assigned_freelancer_id' => $freelancerUser->id,
            'status' => 'admin_reviewed_work',
        ]);

        $jobAssignment = JobAssignment::factory()->create([
            'job_id' => $job->id,
            'freelancer_id' => $freelancerUser->id,
            'assigned_by_admin_id' => $adminProfile->id,
            'status' => 'assigned',
        ]);

        $workSubmission = WorkSubmission::factory()->create([
            'job_assignment_id' => $jobAssignment->id,
            'freelancer_id' => $freelancerUser->id,
            'status' => WorkSubmission::STATUS_PENDING_CLIENT_REVIEW,
        ]);

        // Test approval
        $responseApprove = $this->actingAs($clientUser, 'web')->postJson(route('client.work-submissions.approve', $workSubmission->id));

        $responseApprove->assertStatus(200);
        $this->assertDatabaseHas('work_submissions', [
            'id' => $workSubmission->id,
            'status' => WorkSubmission::STATUS_APPROVED_BY_CLIENT,
        ]);
        $this->assertDatabaseHas('jobs', [
            'id' => $job->id,
            'status' => 'completed',
        ]);

        // The following section was causing a nested transaction error.
        // The RefreshDatabase trait already handles database refreshing for each test.
        // Removed: $this->refreshDatabase();

        // Re-setup for the "request revisions" part of the test
        // This ensures a clean state after the "approval" part has modified the database

        $adminUser_rev = User::factory()->admin()->create();
        $adminProfile_rev = Admin::factory()->create(['user_id' => $adminUser_rev->id]);

        $freelancerUser_rev = User::factory()->freelancer()->create();
        FreelancerProfile::factory()->create(['user_id' => $freelancerUser_rev->id]);

        $clientUser_rev = User::factory()->client()->create();
        ClientProfile::factory()->create(['user_id' => $clientUser_rev->id]);

        $job_rev = Job::factory()->create([
            'user_id' => $clientUser_rev->id,
            'client_id' => $clientUser_rev->id,
            'created_by_admin_id' => $adminProfile_rev->id, // Use adminProfile_rev
            'assigned_freelancer_id' => $freelancerUser_rev->id,
            'status' => 'admin_reviewed_work',
        ]);

        $jobAssignment_rev = JobAssignment::factory()->create([
            'job_id' => $job_rev->id,
            'freelancer_id' => $freelancerUser_rev->id,
            'assigned_by_admin_id' => $adminProfile_rev->id, // Use adminProfile_rev
            'status' => 'assigned',
        ]);

        $workSubmission_rev = WorkSubmission::factory()->create([
            'job_assignment_id' => $jobAssignment_rev->id,
            'freelancer_id' => $freelancerUser_rev->id,
            'status' => WorkSubmission::STATUS_PENDING_CLIENT_REVIEW,
        ]);

        // Test request revisions
        $responseRevision = $this->actingAs($clientUser_rev, 'web')->postJson(route('client.work-submissions.request-revisions', $workSubmission_rev->id), [
            'client_remarks' => 'Please make the logo bigger.',
        ]);

        $responseRevision->assertStatus(200);
        $this->assertDatabaseHas('work_submissions', [
            'id' => $workSubmission_rev->id,
            'status' => WorkSubmission::STATUS_CLIENT_REVISION_REQUESTED,
            'client_remarks' => 'Please make the logo bigger.',
        ]);
        $this->assertDatabaseHas('jobs', [
            'id' => $job_rev->id,
            'status' => 'revisions_requested',
        ]);
    }
}
