<?php

namespace App\Notifications;

use App\Models\WorkSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ClientWorkSubmissionReviewedDbNotification extends Notification implements ShouldQueue
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
        $clientName = $jobAssignment->job->user->name ?? 'The Client'; // Assuming job->user is the client
        $status = $this->workSubmission->status; // e.g., 'approved', 'revision_requested'

        return [
            'title' => 'Work Submission Reviewed by Client',
            'message' => "{$clientName} has reviewed a work submission for job '{$jobTitle}'. Status: {$status}.",
            'work_submission_id' => $this->workSubmission->id,
            'job_assignment_id' => $jobAssignment->id,
            'job_id' => $jobAssignment->job_id,
            'status' => $status,
            'url' => route('admin.jobs.assignments.show', $jobAssignment->id) // Link to admin view of the assignment
        ];
    }
}
