<?php

namespace App\Notifications;

use App\Models\WorkSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class WorkSubmissionStatusUpdatedDbNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public WorkSubmission $workSubmission;

    /**
     * Create a new notification instance.
     */
    public function __construct(WorkSubmission $workSubmission)
    {
        $this->workSubmission = $workSubmission;
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
        $jobAssignment = $this->workSubmission->jobAssignment;
        $jobTitle = $jobAssignment->job->title ?? 'N/A';
        $status = str_replace('_', ' ', ucfirst($this->workSubmission->status)); // e.g., 'Approved', 'Revisions Requested'

        return [
            'title' => 'Work Submission Status Updated',
            'message' => "The status of your work submission for job '{$jobTitle}' has been updated to: {$status}.",
            'work_submission_id' => $this->workSubmission->id,
            'job_assignment_id' => $jobAssignment->id,
            'job_id' => $jobAssignment->job_id,
            'status' => $this->workSubmission->status,
            'url' => route('freelancer.assignments.show', $jobAssignment->id) // Link to the assignment details
        ];
    }
}
