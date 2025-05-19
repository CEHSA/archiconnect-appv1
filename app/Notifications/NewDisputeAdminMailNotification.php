<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Str;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDisputeAdminMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Dispute $dispute;

    /**
     * Create a new notification instance.
     */
    public function __construct(Dispute $dispute)
    {
        $this->dispute = $dispute;
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
        $reporterName = $this->dispute->reporter ? $this->dispute->reporter->name : 'Unknown User';
        $reportedUserName = $this->dispute->reportedUser ? $this->dispute->reportedUser->name : 'Unknown User';
        $jobTitle = $this->dispute->jobAssignment && $this->dispute->jobAssignment->job ? $this->dispute->jobAssignment->job->title : 'N/A';
        $disputeUrl = route('admin.disputes.show', $this->dispute->id);

        return (new MailMessage)
                    ->subject('New Dispute Reported: #' . $this->dispute->id)
                    ->greeting('Hello Admin,')
                    ->line("A new dispute (ID: {$this->dispute->id}) has been reported and requires your attention.")
                    ->line("Reporter: {$reporterName}")
                    ->line("Reported User: {$reportedUserName}")
                    ->line("Related Job Assignment: {$jobTitle} (ID: {$this->dispute->job_assignment_id})")
                    ->line("Reason: " . Str::limit($this->dispute->reason, 150))
                    ->action('View Dispute Details', $disputeUrl)
                    ->line('Please review the dispute and take appropriate action.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // This is primarily a mail notification, but toArray can be used for other channels if added later.
        return [
            'dispute_id' => $this->dispute->id,
            'message' => 'New dispute reported: #' . $this->dispute->id,
        ];
    }
}
