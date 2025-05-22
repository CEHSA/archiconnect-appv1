<?php

namespace App\Notifications;

use App\Models\JobComment;
use App\Models\WorkSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientCommentNotificationToAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;
    public $workSubmission;

    /**
     * Create a new notification instance.
     */
    public function __construct(JobComment $comment, WorkSubmission $workSubmission)
    {
        $this->comment = $comment;
        $this->workSubmission = $workSubmission;
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
        $clientName = $this->comment->user->name ?? 'A client';
        $jobTitle = $this->workSubmission->jobAssignment->job->title ?? 'the job';
        // Admin link to the submission review page
        $url = route('admin.work-submissions.show', $this->workSubmission->id); // Ensure this route exists and is correct for admins

        return (new MailMessage)
                    ->subject("New Client Comment on Work Submission for '{$jobTitle}'")
                    ->greeting('Hello Admin,')
                    ->line("{$clientName} has added a new comment on a work submission for the job: \"{$jobTitle}\".")
                    ->line("Comment: \"{$this->comment->comment_text}\"")
                    ->action('View Submission and Comment', $url)
                    ->line('Please review the comment and take necessary actions.');
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
            'job_id' => $this->workSubmission->jobAssignment->job_id,
            'job_title' => $this->workSubmission->jobAssignment->job->title,
            'comment_id' => $this->comment->id,
            'comment_text' => $this->comment->comment_text,
            'commenter_name' => $this->comment->user->name,
            'commenter_role' => 'Client',
            'message' => "{$this->comment->user->name} commented on a work submission for job: {$this->workSubmission->jobAssignment->job->title}.",
            // Admin link to the submission review page
            'url' => route('admin.work-submissions.show', $this->workSubmission->id),
        ];
    }
}
