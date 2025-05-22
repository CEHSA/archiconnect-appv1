<?php

namespace App\Notifications;

use App\Models\Dispute;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class DisputeStatusUpdatedMailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Dispute $dispute;
    public User $recipient;

    /**
     * Create a new notification instance.
     */
    public function __construct(Dispute $dispute, User $recipient)
    {
        $this->dispute = $dispute;
        $this->recipient = $recipient;
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
        $jobTitle = $this->dispute->jobAssignment && $this->dispute->jobAssignment->job ? $this->dispute->jobAssignment->job->title : 'N/A';
        $statusText = ucfirst(str_replace('_', ' ', $this->dispute->status));
        
        $subject = "Update on Dispute ID: {$this->dispute->id} - Status: {$statusText}";
        $greeting = "Hello {$notifiable->name},";
        
        $line1 = "The dispute (ID: {$this->dispute->id}) concerning the job assignment '{$jobTitle}' has been updated.";
        $line2 = "The new status is: **{$statusText}**.";
        
        $actionText = 'View Dashboard';
        $actionUrl = route('dashboard');

        if ($this->recipient->hasRole(User::ROLE_CLIENT) && $this->dispute->jobAssignment && $this->dispute->jobAssignment->job) {
            $actionText = 'View Job Details';
            $actionUrl = route('client.jobs.show', $this->dispute->jobAssignment->job_id);
        } elseif ($this->recipient->hasRole(User::ROLE_FREELANCER) && $this->dispute->jobAssignment) {
            $actionText = 'View Assignment Details';
            $actionUrl = route('freelancer.assignments.show', $this->dispute->job_assignment_id);
        }

        $mailMessage = (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line1)
                    ->line($line2);

        if ($this->dispute->admin_remarks) {
            $mailMessage->line("Administrator Remarks: " . $this->dispute->admin_remarks);
        }
        
        $mailMessage->action($actionText, $actionUrl)
                    ->line('If you have any questions, please contact support.');

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
            'dispute_id' => $this->dispute->id,
            'new_status' => $this->dispute->status,
        ];
    }
}
