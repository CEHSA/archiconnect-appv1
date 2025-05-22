<?php

namespace App\Notifications;

use App\Models\Dispute;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewDisputeAdminDbNotification extends Notification implements ShouldQueue
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
        $reporterName = $this->dispute->reporter ? $this->dispute->reporter->name : 'Unknown User';
        $reportedUserName = $this->dispute->reportedUser ? $this->dispute->reportedUser->name : 'Unknown User';
        $jobTitle = $this->dispute->jobAssignment && $this->dispute->jobAssignment->job ? $this->dispute->jobAssignment->job->title : 'N/A';

        return [
            'dispute_id' => $this->dispute->id,
            'job_assignment_id' => $this->dispute->job_assignment_id,
            'job_title' => $jobTitle,
            'reporter_name' => $reporterName,
            'reported_user_name' => $reportedUserName,
            'message' => "A new dispute (ID: {$this->dispute->id}) has been reported by {$reporterName} against {$reportedUserName} regarding job assignment '{$jobTitle}'.",
            'link' => route('admin.disputes.show', $this->dispute->id),
            'type' => 'dispute_new',
        ];
    }
}
