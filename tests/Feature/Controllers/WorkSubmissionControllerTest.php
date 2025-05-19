<?php

namespace Tests\Feature\Controllers;

use App\Models\JobAssignment;


use App\Models\User;


use App\Models\WorkSubmission;


use Illuminate\Foundation\Testing\RefreshDatabase;


use Illuminate\Http\UploadedFile;


use Illuminate\Support\Facades\Storage;


use Tests\TestCase;
class WorkSubmissionControllerTest extends TestCase
{
    use RefreshDatabase;



    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
    }
    #[Test]
    public function unauthenticated_users_cannot_access_work_submission_routes()
    {
        $assignment = JobAssignment::factory()->create();
        
        $this->get(route('freelancer.assignments.submissions.create', $assignment))
            ->assertRedirect(route('login'));
            
        $this->post(route('freelancer.assignments.submissions.store', $assignment))
            ->assertRedirect(route('login'));
    }
    #[Test]
    public function freelancer_can_view_submission_form_for_their_assignment()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $assignment = JobAssignment::factory()->create([
            'freelancer_id' => $freelancer->id,
            'status' => 'in_progress'
        ]);
        
        $this->actingAs($freelancer)
            ->get(route('freelancer.assignments.submissions.create', $assignment))
            ->assertStatus(200);
    }
    #[Test]
    public function freelancer_cannot_view_submission_form_for_other_freelancers_assignments()
    {
        $freelancer1 = User::factory()->create(['role' => 'freelancer']);
        $freelancer2 = User::factory()->create(['role' => 'freelancer']);
        $assignment = JobAssignment::factory()->create([
            'freelancer_id' => $freelancer2->id,
            'status' => 'in_progress'
        ]);
        
        $this->actingAs($freelancer1)
            ->get(route('freelancer.assignments.submissions.create', $assignment))
            ->assertStatus(403);
    }
    #[Test]
    public function freelancer_can_submit_work_for_their_assignment()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $assignment = JobAssignment::factory()->create([
            'freelancer_id' => $freelancer->id,
            'status' => 'in_progress'
        ]);
        
        $file = UploadedFile::fake()->create('document.pdf', 1000);
        
        $submissionData = [
            'title' => 'Project Deliverable',
            'description' => 'Final project files',
            'submission_file' => $file,
        ];
        
        $this->actingAs($freelancer)
            ->post(route('freelancer.assignments.submissions.store', $assignment), $submissionData)
            ->assertRedirect(route('freelancer.assignments.show', $assignment->id));
            
        $this->assertDatabaseHas('work_submissions', [
            'job_assignment_id' => $assignment->id,
            'freelancer_id' => $freelancer->id,
            'title' => 'Project Deliverable',
            'description' => 'Final project files',
            'original_filename' => 'document.pdf',
            'mime_type' => 'application/pdf',
            'status' => 'submitted',
        ]);
        
        // Check that the file was stored
        Storage::disk('private')->assertExists('*');
    }
    #[Test]
    public function freelancer_cannot_submit_work_for_other_freelancers_assignments()
    {
        $freelancer1 = User::factory()->create(['role' => 'freelancer']);
        $freelancer2 = User::factory()->create(['role' => 'freelancer']);
        $assignment = JobAssignment::factory()->create([
            'freelancer_id' => $freelancer2->id,
            'status' => 'in_progress'
        ]);
        
        $file = UploadedFile::fake()->create('document.pdf', 1000);
        
        $submissionData = [
            'title' => 'Project Deliverable',
            'description' => 'Final project files',
            'submission_file' => $file,
        ];
        
        $this->actingAs($freelancer1)
            ->post(route('freelancer.assignments.submissions.store', $assignment), $submissionData)
            ->assertStatus(403);
    }
    #[Test]
    public function submission_updates_assignment_status_if_first_submission()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $assignment = JobAssignment::factory()->create([
            'freelancer_id' => $freelancer->id,
            'status' => 'accepted'
        ]);
        
        $file = UploadedFile::fake()->create('document.pdf', 1000);
        
        $submissionData = [
            'title' => 'Project Deliverable',
            'description' => 'Final project files',
            'submission_file' => $file,
        ];
        
        $this->actingAs($freelancer)
            ->post(route('freelancer.assignments.submissions.store', $assignment), $submissionData);
            
        $this->assertDatabaseHas('job_assignments', [
            'id' => $assignment->id,
            'status' => 'in_progress'
        ]);
    }
}
