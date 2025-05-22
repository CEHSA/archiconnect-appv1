<?php

namespace App\Notifications;

use App\Models\JobAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FreelancerRespondedToAssignmentDbNotification extends Notification implements ShouldQueue
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
        $freelancerName = $this->jobAssignment->freelancer->name;
        $jobTitle = $this->jobAssignment->job->title;
        $status = $this->jobAssignment->status; // 'accepted' or 'declined'

        return [
            'title' => 'Freelancer Assignment Response',
            'message' => "Freelancer {$freelancerName} has {$status} the assignment for job: '{$jobTitle}'.",
            'job_assignment_id' => $this->jobAssignment->id,
            'job_id' => $this->jobAssignment->job_id,
            'freelancer_id' => $this->jobAssignment->freelancer_id,
            'status' => $status,
            'url' => route('admin.assignments.show', $this->jobAssignment->id) // Link to admin view of the assignment
        ];
    }
}
