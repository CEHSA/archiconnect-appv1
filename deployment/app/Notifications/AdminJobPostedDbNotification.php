<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class AdminJobPostedDbNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Job $job;

    /**
     * Create a new notification instance.
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
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
            'title' => 'New Job Available',
            'message' => "A new job '{$this->job->title}' has been posted and is available for application or assignment.",
            'job_id' => $this->job->id,
            'url' => route('freelancer.jobs.show', $this->job->id) // Link to freelancer job view
        ];
    }
}
