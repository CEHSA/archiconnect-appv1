<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\WorkSubmission; // Add this line

class ClientWorkSubmissionReviewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public WorkSubmission $workSubmission)
    {
        $this->workSubmission->load(['jobAssignment.job', 'jobAssignment.freelancer', 'jobAssignment.job.client']); // Eager load relationships
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
        $submission = $this->workSubmission;
        $client = $submission->jobAssignment->job->client;
        $freelancer = $submission->jobAssignment->freelancer;
        $jobTitle = $submission->jobAssignment->job->title;
        $reviewStatus = ucfirst(str_replace('_', ' ', $submission->status));
        $reviewRemarks = $submission->client_remarks;

        $mailMessage = (new MailMessage)
            ->subject("Client Review: Work Submission for \"{$jobTitle}\"")
            ->greeting('Hello Admin,')
            ->line("Client **{$client->name}** has reviewed the work submission for the job **\"{$jobTitle}\"**.")
            ->line("The review status is: **{$reviewStatus}**.");

        if ($reviewRemarks) {
            $mailMessage->line("Client Remarks: \"{$reviewRemarks}\"");
        }

        $mailMessage->action('View Submission Details', route('admin.work-submissions.show', $submission))
                    ->line('Please review the submission and take appropriate action.');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'work_submission_id' => $this->workSubmission->id,
            'job_title' => $this->workSubmission->jobAssignment->job->title,
            'client_name' => $this->workSubmission->jobAssignment->job->client->name,
            'freelancer_name' => $this->workSubmission->jobAssignment->freelancer->name,
            'status' => $this->workSubmission->status,
            'client_remarks' => $this->workSubmission->client_remarks,
            'url' => route('admin.work-submissions.show', $this->workSubmission),
        ];
    }
}
