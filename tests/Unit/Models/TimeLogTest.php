<?php

namespace Tests\Unit\Models;

use App\Models\TimeLog;

use App\Models\JobAssignment;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;


use Tests\TestCase;
class TimeLogTest extends TestCase
{
    use RefreshDatabase;


    #[Test]
    public function it_has_correct_fillable_attributes()
    {
        $timeLog = new TimeLog();
        
        $this->assertEquals([
            'job_assignment_id',
            'freelancer_id',
            'start_time',
            'end_time',
            'duration',
            'task_description',
            'is_auto_stopped',
        ], $timeLog->getFillable());
    }

    #[Test]
    public function it_belongs_to_a_job_assignment()
    {
        $assignment = JobAssignment::factory()->create();
        $timeLog = TimeLog::factory()->create(['job_assignment_id' => $assignment->id]);

        $this->assertInstanceOf(JobAssignment::class, $timeLog->jobAssignment);
        $this->assertEquals($assignment->id, $timeLog->jobAssignment->id);
    }

    #[Test]
    public function it_belongs_to_a_freelancer()
    {
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $timeLog = TimeLog::factory()->create(['freelancer_id' => $freelancer->id]);

        $this->assertInstanceOf(User::class, $timeLog->freelancer);
        $this->assertEquals($freelancer->id, $timeLog->freelancer->id);
    }

    #[Test]
    public function it_can_be_created_with_valid_data()
    {
        $assignment = JobAssignment::factory()->create();
        $freelancer = User::factory()->create(['role' => 'freelancer']);
        
        $timeLogData = [
            'job_assignment_id' => $assignment->id,
            'freelancer_id' => $freelancer->id,
            'start_time' => now()->subHours(2),
            'end_time' => now()->subHour(),
            'duration' => 60, // 60 minutes
            'task_description' => 'Working on frontend implementation',
            'is_auto_stopped' => false,
        ];

        $timeLog = TimeLog::create($timeLogData);

        $this->assertInstanceOf(TimeLog::class, $timeLog);
        $this->assertDatabaseHas('time_logs', $timeLogData);
    }
}
