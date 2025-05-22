<?php

namespace App\Notifications;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class JobApplicationStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public JobApplication $application;
    public string $oldStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(JobApplication $application, string $oldStatus)
    {
        $this->application = $application;
        $this->oldStatus = $oldStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail']; // Or just ['database']
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $jobTitle = $this->application->job->title;
        $newStatus = $this->application->status;
        // $applicationUrl = route('freelancer.applications.show', $this->application->id); // TODO: Create freelancer view for their applications

        $subject = "Update on your application for: {$jobTitle}";
        $line = "The status of your application for the job \"{$jobTitle}\" has been updated from '{$this->oldStatus}' to '{$newStatus}'.";
        
        $mailMessage = (new MailMessage)
                    ->subject($subject)
                    ->greeting("Hello {$notifiable->name},")
                    ->line($line);

        // Optionally add more details or actions based on status
        if ($newStatus === 'accepted_for_assignment') {
            $mailMessage->line('Congratulations! Further details regarding the assignment will follow.');
            // $mailMessage->action('View Assignment Details', route('freelancer.assignments.show', $this->application->job_assignment_id)); // If an assignment is created
        } elseif ($newStatus === 'rejected') {
            $mailMessage->line('We appreciate your interest and encourage you to apply for other suitable positions.');
        }
        
        // $mailMessage->action('View Application Status', $applicationUrl); // General link
        $mailMessage->line('Thank you for using Architex Axis.');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $jobTitle = $this->application->job->title;
        $newStatus = $this->application->status;

        return [
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'job_title' => $jobTitle,
            'old_status' => $this->oldStatus,
            'new_status' => $newStatus,
            'message' => "Your application status for \"{$jobTitle}\" changed from '{$this->oldStatus}' to '{$newStatus}'.",
            'link' => route('freelancer.jobs.show', $this->application->job_id), // TODO: Ideally, link to a page showing application status or list of applications
        ];
    }
}
