<?php

namespace App\Notifications;

use App\Models\TimeLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TimeLogReviewedNotification extends Notification implements ShouldQueue
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
        $status = ucfirst($this->timeLog->status);
        $adminComments = $this->timeLog->admin_comments ?: 'No comments provided.';
        $freelancerUrl = route('freelancer.assignments.show', $this->timeLog->assignmentTask->jobAssignment_id); // Link to assignment or task page

        return (new MailMessage)
                    ->subject("Time Log for Task '{$taskName}' has been {$status}")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("Your time log for the task '{$taskName}' (Job: '{$jobName}') has been {$status}.")
                    ->line("Duration: " . $this->timeLog->duration_for_humans)
                    ->line("Admin Comments: {$adminComments}")
                    ->action('View Assignment', $freelancerUrl)
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
            'task_name' => $this->timeLog->assignmentTask->title,
            'job_name' => $this->timeLog->assignmentTask->jobAssignment->job->title,
            'status' => $this->timeLog->status,
            'admin_comments' => $this->timeLog->admin_comments,
            'message' => "Your time log for task '{$this->timeLog->assignmentTask->title}' has been " . ucfirst($this->timeLog->status) . ".",
            'link' => route('freelancer.assignments.show', $this->timeLog->assignmentTask->jobAssignment_id),
        ];
    }
}
