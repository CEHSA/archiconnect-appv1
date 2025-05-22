<?php

namespace App\Notifications;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewJobPostedToFreelancerNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Job $job;

    /**
     * Create a new notification instance.
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // TODO: Add 'mail' once email templates are designed and settings configured
        return ['database']; 
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $clientName = $this->job->user->name ?? 'A client';
        $jobUrl = route('freelancer.posted-jobs.index'); // Or a direct link to the job if available

        return (new MailMessage)
                    ->subject('New Job Opportunity: ' . $this->job->title)
                    ->greeting('Hello ' . $notifiable->name . ',')
                    ->line($clientName . ' has posted a new job that might interest you: "' . $this->job->title . '".')
                    ->line('Budget: ' . ($this->job->budget ? '$' . number_format($this->job->budget, 2) : 'Not specified'))
                    ->action('View Job Details', $jobUrl)
                    ->line('Thank you for using Architex Axis!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'message' => 'A new job "' . $this->job->title . '" has been posted that matches your profile.',
            'link' => route('freelancer.jobs.show', $this->job->id), // Link to the specific job details page
        ];
    }

    /**
     * Get the database representation of the notification.
     *
     * @param  object  $notifiable
     * @return array
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'job_id' => $this->job->id,
            'job_title' => $this->job->title,
            'message' => 'A new job "' . $this->job->title . '" has been posted that may interest you.',
            'link' => route('freelancer.jobs.show', $this->job->id),
        ];
    }
}
