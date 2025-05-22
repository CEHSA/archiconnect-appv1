<?php

namespace App\Notifications;

use App\Models\TimeLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TimerAutoStoppedDbNotification extends Notification implements ShouldQueue
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
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $jobAssignment = $this->timeLog->jobAssignment;
        $jobTitle = $jobAssignment->job->title ?? 'N/A';
        $endTime = $this->timeLog->end_time ? $this->timeLog->end_time->format('Y-m-d H:i:s') : 'N/A';

        return [
            'title' => 'Timer Auto-Stopped',
            'message' => "Your timer for job '{$jobTitle}' was automatically stopped at {$endTime} as it exceeded the maximum allowed duration.",
            'time_log_id' => $this->timeLog->id,
            'job_assignment_id' => $jobAssignment->id,
            'job_id' => $jobAssignment->job_id,
            'url' => route('freelancer.assignments.show', $jobAssignment->id) // Link to the assignment details
        ];
    }
}
