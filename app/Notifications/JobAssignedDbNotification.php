<?php

namespace App\Notifications;

use App\Models\JobAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class JobAssignedDbNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public JobAssignment $jobAssignment;

    /**
     * Create a new notification instance.
     */
    public function __construct(JobAssignment $jobAssignment)
    {
        $this->jobAssignment = $jobAssignment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $jobTitle = $this->jobAssignment->job->title;

        return [
            'title' => 'New Job Assignment',
            'message' => "You have been assigned to a new job: '{$jobTitle}'. Please review and respond.",
            'job_assignment_id' => $this->jobAssignment->id,
            'job_id' => $this->jobAssignment->job_id,
            'url' => route('freelancer.assignments.show', $this->jobAssignment->id) // Link to freelancer assignment view
        ];
    }
}
