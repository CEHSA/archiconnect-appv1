<?php

namespace App\Notifications;

use App\Models\JobComment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobCommentStatusUpdatedDbNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public JobComment $jobComment)
    {
        //
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->line('The introduction to the notification.')
                    ->action('Notification Action', url('/'))
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
            'job_comment_id' => $this->jobComment->id,
            'job_title' => $this->jobComment->jobAssignment->job->title,
            'status' => $this->jobComment->status,
            'url' => route('admin.jobs.assignments.show', $this->jobComment->jobAssignment->id) . '#comment-' . $this->jobComment->id, // Assuming comments are displayed on assignment show page
            'message' => 'The status of your comment on job "' . $this->jobComment->jobAssignment->job->title . '" has been updated to "' . $this->jobComment->status . '".',
        ];
    }
}
