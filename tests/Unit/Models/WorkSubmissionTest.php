<?php

namespace Tests\Unit\Models;

use App\Models\WorkSubmission;

use App\Models\JobAssignment;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;


use Tests\TestCase;
class WorkSubmissionTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function it_has_correct_fillable_attributes()
    {
        $submission = new WorkSubmission();
        
        $this->assertEquals([
            'job_assignment_id',
            'freelancer_id',
            'admin_id',
            'title',
            'description',
            'file_path',
            'original_filename',
            'mime_type',
            'size',
            'status',
            'submitted_at',
            'reviewed_at',
            'admin_remarks',
        ], $submission->getFillable());
    }

    #[Test]
    public function it_has_correct_casts()
    {
        $submission = new WorkSubmission();
        
        $this->assertEquals([
            'submitted_at' => 'datetime',
            'reviewed_at' => 'datetime',
            'size' => 'integer',
        ], $submission->getCasts());
    }

    #[Test]
    public function it_belongs_to_a_job_assignment()
    {
        $assignment = JobAssignment::factory()->create();
        $submission = WorkSubmission::factory()->create(['job_assignment_id' => $assignment->id]);

        $this->assertInstanceOf(JobAssignment::class, $submission->jobAssignment);
        $this->assertEquals($assignment->id, $submission->jobAssignment->id);
    }

    #[Test]
    public function it_belongs_to_a_freelancer()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $submission = WorkSubmission::factory()->create(['freelancer_id' => $freelancer->id]);

        $this->assertInstanceOf(User::class, $submission->freelancer);
        $this->assertEquals($freelancer->id, $submission->freelancer->id);
    }

    #[Test]
    public function it_can_be_created_with_valid_data()
    {
        $assignment = JobAssignment::factory()->create();
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        
        $submissionData = [
            'job_assignment_id' => $assignment->id,
            'freelancer_id' => $freelancer->id,
            'title' => 'Project Deliverable',
            'description' => 'Final project files',
            'file_path' => 'submissions/project-files.zip',
            'original_filename' => 'project-files.zip',
            'mime_type' => 'application/zip',
            'size' => 1024000,
            'status' => 'submitted',
            'submitted_at' => now(),
        ];

        $submission = WorkSubmission::create($submissionData);

        $this->assertInstanceOf(WorkSubmission::class, $submission);
        $this->assertDatabaseHas('work_submissions', $submissionData);
    }
}
