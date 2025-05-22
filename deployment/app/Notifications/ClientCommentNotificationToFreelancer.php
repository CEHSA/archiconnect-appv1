<?php

namespace App\Notifications;

use App\Models\JobComment;
use App\Models\WorkSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ClientCommentNotificationToFreelancer extends Notification implements ShouldQueue
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
        $clientName = $this->comment->user->name ?? 'The client';
        $jobTitle = $this->workSubmission->jobAssignment->job->title ?? 'your job submission';
        // Freelancer link to their assignment/submission view.
        // Assuming a route like 'freelancer.assignments.show' or similar for viewing submission details.
        // This might need adjustment based on actual freelancer routes.
        $url = route('freelancer.assignments.show', $this->workSubmission->job_assignment_id); // Adjust if route is different

        return (new MailMessage)
                    ->subject("New Client Comment on Your Submission for '{$jobTitle}'")
                    ->greeting('Hello ' . ($notifiable->name ?? 'Freelancer') . ',')
                    ->line("{$clientName} has added a new comment on your work submission for the job: \"{$jobTitle}\".")
                    ->line("Comment: \"{$this->comment->comment_text}\"")
                    ->action('View Submission and Comment', $url)
                    ->line('Please review the comment and make any necessary revisions.');
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
            'job_assignment_id' => $this->workSubmission->job_assignment_id,
            'job_title' => $this->workSubmission->jobAssignment->job->title,
            'comment_id' => $this->comment->id,
            'comment_text' => $this->comment->comment_text,
            'commenter_name' => $this->comment->user->name,
            'commenter_role' => 'Client',
            'message' => "{$this->comment->user->name} commented on your submission for job: {$this->workSubmission->jobAssignment->job->title}.",
            // Freelancer link to their assignment/submission view
            'url' => route('freelancer.assignments.show', $this->workSubmission->job_assignment_id), // Adjust if route is different
        ];
    }
}
