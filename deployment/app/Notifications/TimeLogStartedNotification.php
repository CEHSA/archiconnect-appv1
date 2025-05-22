<?php

namespace App\Notifications;

use App\Models\TimeLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TimeLogStartedNotification extends Notification implements ShouldQueue
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
        $freelancerName = $this->timeLog->freelancer->name;
        $taskName = $this->timeLog->assignmentTask->title;
        $jobName = $this->timeLog->assignmentTask->jobAssignment->job->title;
        // $adminUrl = route('admin.time-logs.show', $this->timeLog->id); // Assuming you'll have an admin view for time logs

        return (new MailMessage)
                    ->subject("Timer Started: {$freelancerName} on Task '{$taskName}'")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("Freelancer {$freelancerName} has started the timer for the task '{$taskName}' on job '{$jobName}'.")
                    ->line("Start Time: " . $this->timeLog->start_time->format('Y-m-d H:i:s'))
                    // ->action('View Time Log', $adminUrl)
                    ->line('Thank you for using our application!');
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
            'freelancer_name' => $this->timeLog->freelancer->name,
            'task_name' => $this->timeLog->assignmentTask->title,
            'job_name' => $this->timeLog->assignmentTask->jobAssignment->job->title,
            'message' => "Timer started by {$this->timeLog->freelancer->name} for task '{$this->timeLog->assignmentTask->title}'.",
            // 'link' => route('admin.time-logs.show', $this->timeLog->id), // For in-app notification link
        ];
    }
}
