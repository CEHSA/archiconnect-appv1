<?php

namespace Tests\Unit\Events;

use App\Events\JobAssigned;
use App\Listeners\SendFreelancerAssignmentNotification;
use App\Models\JobAssignment;
use App\Models\User;
use App\Notifications\JobAssignmentNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

use Tests\TestCase;

class JobAssignedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function job_assigned_event_dispatches_correctly()
    {
        Event::fake();

        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $assignment = JobAssignment::factory()->create(['freelancer_id' => $freelancer->id]);

        // Dispatch the event
        event(new JobAssigned($assignment));

        // Assert the event was dispatched
        Event::assertDispatched(JobAssigned::class, function ($event) use ($assignment) {
            return $event->jobAssignment->id === $assignment->id;
        });
    }

    #[Test]
    public function job_assigned_listener_sends_notification()
    {
        Notification::fake();

        $freelancer = User::factory()->create(['role' => 'freelancer']);
        $assignment = JobAssignment::factory()->create(['freelancer_id' => $freelancer->id]);

        // Create the event
        $event = new JobAssigned($assignment);

        // Create the listener
        $listener = new SendFreelancerAssignmentNotification();

        // Handle the event
        $listener->handle($event);

        // Assert a notification was sent to the freelancer
        Notification::assertSentTo(
            $freelancer,
            JobAssignmentNotification::class,
            function ($notification, $channels) use ($assignment) {
                return $notification->jobAssignment->id === $assignment->id;
            }
        );
    }
}
