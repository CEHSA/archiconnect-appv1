<?php

namespace App\Notifications;

use App\Models\TimeLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovedTimeLogNotificationToClient extends Notification implements ShouldQueue
{
    use Queueable;

    public TimeLog $timeLog;

    /**
     * Create a new notification instance.
     */
    public function __construct(TimeLog $timeLog)
    {
        $this->timeLog = $timeLog;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $taskName = $this->timeLog->assignmentTask->title;
        $jobName = $this->timeLog->assignmentTask->jobAssignment->job->title;
        $freelancerName = $this->timeLog->freelancer->name;
        $duration = $this->timeLog->duration_for_humans;
        $clientJobUrl = route('client.jobs.show', $this->timeLog->assignmentTask->jobAssignment->job_id);

        return (new MailMessage)
                    ->subject("Time Log Approved for Task '{$taskName}' on Job '{$jobName}'")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("A time log submitted by freelancer {$freelancerName} for the task '{$taskName}' (Job: '{$jobName}') has been approved by an administrator.")
                    ->line("Duration Logged: {$duration}.")
                    ->line("Freelancer Comments: " . ($this->timeLog->freelancer_comments ?: 'N/A'))
                    ->line("Admin Comments: " . ($this->timeLog->admin_comments ?: 'N/A'))
                    ->action('View Job Details', $clientJobUrl)
                    ->line('This is for your information.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'time_log_id' => $this->timeLog->id,
            'task_name' => $this->timeLog->assignmentTask->title,
            'job_name' => $this->timeLog->assignmentTask->jobAssignment->job->title,
            'freelancer_name' => $this->timeLog->freelancer->name,
            'duration_for_humans' => $this->timeLog->duration_for_humans,
            'message' => "A time log for task '{$this->timeLog->assignmentTask->title}' (Job: {$this->timeLog->assignmentTask->jobAssignment->job->title}) by {$this->timeLog->freelancer->name} has been approved. Duration: {$this->timeLog->duration_for_humans}.",
            'link' => route('client.jobs.show', $this->timeLog->assignmentTask->jobAssignment->job_id),
        ];
    }
}
