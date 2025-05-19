<?php

namespace App\Notifications;

use App\Models\Dispute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class UserReportedInDisputeMailNotification extends Notification implements ShouldQueue
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
        $reporterName = $this->dispute->reporter ? $this->dispute->reporter->name : 'A user';
        $jobTitle = $this->dispute->jobAssignment && $this->dispute->jobAssignment->job ? $this->dispute->jobAssignment->job->title : 'N/A';
        
        // The reported user doesn't have a direct action link for the dispute itself,
        // as admins handle it. They are just being informed.
        $dashboardUrl = route('dashboard'); 

        return (new MailMessage)
                    ->subject("Notification: A Dispute Has Been Reported Involving You (ID: {$this->dispute->id})")
                    ->greeting("Hello {$notifiable->name},")
                    ->line("This email is to inform you that a dispute (ID: {$this->dispute->id}) has been reported by {$reporterName} concerning the job assignment: '{$jobTitle}'.")
                    ->line("The reason provided for the dispute is: \"" . Str::limit($this->dispute->reason, 200) . "\"")
                    ->line("An administrator will review this dispute and may contact you if further information is required.")
                    ->line("No immediate action is required from you at this moment unless contacted by an administrator.")
                    ->action('Go to Dashboard', $dashboardUrl)
                    ->line('Thank you for your understanding.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'dispute_id' => $this->dispute->id,
            'message' => 'A dispute has been reported involving you: #' . $this->dispute->id,
        ];
    }
}
