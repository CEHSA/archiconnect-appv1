<?php

namespace App\Notifications;

use App\Models\JobAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\User;

class JobCompletedDbNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public JobAssignment $jobAssignment;
    public User $recipient; // To determine the correct URL

    /**
     * Create a new notification instance.
     */
    public function __construct(JobAssignment $jobAssignment, User $recipient)
    {
        $this->jobAssignment = $jobAssignment;
        $this->recipient = $recipient;
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
        $job = $this->jobAssignment->job;
        $message = "The job '{$job->title}' has been marked as completed.";
        $url = '#'; // Default URL

        if ($this->recipient->hasRole(User::ROLE_CLIENT)) {
            $url = route('client.jobs.show', $job->id);
            $message = "The job '{$job->title}' you posted has been marked as completed by the admin.";
        } elseif ($this->recipient->hasRole(User::ROLE_FREELANCER)) {
            $url = route('freelancer.assignments.show', $this->jobAssignment->id);
             $message = "Your assigned job '{$job->title}' has been marked as completed by the admin.";
        } elseif ($this->recipient->hasRole(User::ROLE_ADMIN)) {
            // Admin might not need a specific link here, or link to admin job view
            $url = route('admin.jobs.show', $job->id);
        }


        return [
            'title' => 'Job Completed',
            'message' => $message,
            'job_id' => $job->id,
            'job_assignment_id' => $this->jobAssignment->id,
            'url' => $url,
        ];
    }
}
