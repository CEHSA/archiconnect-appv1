<?php

namespace App\Notifications;

use App\Models\JobApplication;
use App\Models\User; // For Admin model if needed
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewJobApplicationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public JobApplication $application;

    /**
     * Create a new notification instance.
     */
    public function __construct(JobApplication $application)
    {
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail']; // Add 'mail' if email notifications are desired
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $freelancerName = $this->application->freelancer->name;
        $jobTitle = $this->application->job->title;
        // $applicationUrl = route('admin.job-applications.show', $this->application->id); // Adjust if client also views this

        $subject = "New Application for Job: {$jobTitle}";
        $greeting = "Hello,";
        $line = "Freelancer {$freelancerName} has submitted an application for the job: \"{$jobTitle}\".";
        
        // Customize for admin vs client if needed
        // if ($notifiable instanceof Admin) { // Assuming Admin model or role check
        //    $greeting = "Hello Admin,";
        // } elseif ($notifiable->id === $this->application->job->user_id) { // Client who posted the job
        //    $greeting = "Hello {$notifiable->name},";
        // }


        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line)
                    // ->action('View Application', $applicationUrl) // TODO: Create admin/client view for applications
                    ->line('You can review this application in the admin panel.'); // Placeholder
    }

    /**
     * Get the array representation of the notification (for database).
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $freelancerName = $this->application->freelancer->name;
        $jobTitle = $this->application->job->title;

        return [
            'application_id' => $this->application->id,
            'job_id' => $this->application->job_id,
            'job_title' => $jobTitle,
            'freelancer_name' => $freelancerName,
            'message' => "New application from {$freelancerName} for job: \"{$jobTitle}\".",
            'link' => route('admin.job-applications.show', $this->application->id), 
        ];
    }
}
