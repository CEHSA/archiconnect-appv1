<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserReportedInDisputeDbNotification extends Notification implements ShouldQueue
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
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $reporterName = $this->dispute->reporter ? $this->dispute->reporter->name : 'A user';
        $jobTitle = $this->dispute->jobAssignment && $this->dispute->jobAssignment->job ? $this->dispute->jobAssignment->job->title : 'N/A';
        
        // Determine the appropriate link based on user role (though disputes are generally handled by admin)
        // For now, a generic dashboard link or no link might be okay for the reported user's DB notification.
        // Admins will have a direct link. Users might just be informed.
        $link = route('dashboard'); // General dashboard link

        return [
            'dispute_id' => $this->dispute->id,
            'job_assignment_id' => $this->dispute->job_assignment_id,
            'job_title' => $jobTitle,
            'reporter_name' => $reporterName,
            'message' => "A dispute (ID: {$this->dispute->id}) has been reported against you by {$reporterName} regarding job assignment '{$jobTitle}'. An admin will review this shortly.",
            'link' => $link, // Or null if no specific action link for the user from this notification
            'type' => 'dispute_reported_against_you',
        ];
    }
}
