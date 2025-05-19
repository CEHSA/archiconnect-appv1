<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\TimeLog;
use Carbon\Carbon;

class TimerAutoStoppedNotification extends Notification implements ShouldQueue
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $jobTitle = $this->timeLog->jobAssignment->job->title ?? 'N/A';
        $startTime = Carbon::parse($this->timeLog->start_time)->format('M d, Y H:i:s');
        $endTime = Carbon::parse($this->timeLog->end_time)->format('M d, Y H:i:s');
        $duration = gmdate("H:i:s", $this->timeLog->duration);
        $assignmentUrl = route('freelancer.assignments.show', $this->timeLog->job_assignment_id);

        return (new MailMessage)
                    ->subject('Your Timer Was Automatically Stopped')
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line("One of your active time logs has been automatically stopped because it exceeded the maximum allowed duration.")
                    ->line("Job: **{$jobTitle}**")
                    ->line("Task Description: " . ($this->timeLog->task_description ?: 'Not specified'))
                    ->line("Started at: {$startTime}")
                    ->line("Automatically stopped at: {$endTime}")
                    ->line("Logged duration: {$duration}")
                    ->line("Please ensure you restart the timer if you are still working on this task, or log any additional time manually if necessary (feature to be implemented).")
                    ->action('View Job Assignment', $assignmentUrl)
                    ->line('If you believe this was an error, please contact an administrator.')
                    ->line('Thank you for using Archiconnect.');
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
            'job_assignment_id' => $this->timeLog->job_assignment_id,
            'job_title' => $this->timeLog->jobAssignment->job->title ?? 'N/A',
            'message' => 'Your timer was automatically stopped.',
        ];
    }
}
