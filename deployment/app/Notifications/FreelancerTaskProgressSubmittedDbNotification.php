<?php

namespace App\Notifications;

use App\Models\TaskProgress;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FreelancerTaskProgressSubmittedDbNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public TaskProgress $taskProgress;

    /**
     * Create a new notification instance.
     */
    public function __construct(TaskProgress $taskProgress)
    {
        $this->taskProgress = $taskProgress;
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
        return [
            'title' => 'New Task Progress Submitted',
            'message' => "Freelancer {$this->taskProgress->freelancer->name} has submitted a progress update for assignment: {$this->taskProgress->jobAssignment->job->title}.",
            'task_progress_id' => $this->taskProgress->id,
            'job_assignment_id' => $this->taskProgress->job_assignment_id,
            'url' => route('admin.assignments.show', $this->taskProgress->job_assignment_id) // Example URL
        ];
    }
}
