<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Job;

class JobCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Job $job,
        public string $completionStatus,
        public string $notes
    ) {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("Job Completed: {$this->job->title}")
            ->line("Your job '{$this->job->title}' has been marked as {$this->completionStatus}.")
            ->line("Notes: {$this->notes}")
            ->action('View Job', route('jobs.show', $this->job));
    }

    public function toArray($notifiable)
    {
        return [
            'job_id' => $this->job->id,
            'title' => $this->job->title,
            'status' => $this->completionStatus,
            'notes' => $this->notes,
            'url' => route('jobs.show', $this->job)
        ];
    }
}
