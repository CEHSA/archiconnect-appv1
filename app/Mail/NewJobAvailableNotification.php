<?php

namespace App\Mail;

use App\Models\Job;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewJobAvailableNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Job $job;
    public User $freelancer;

    /**
     * Create a new message instance.
     */
    public function __construct(Job $job, User $freelancer)
    {
        $this->job = $job;
        $this->freelancer = $freelancer;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Job Opportunity: ' . $this->job->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.jobs.new-job-available',
            with: [
                'jobTitle' => $this->job->title,
                'jobDescription' => $this->job->description,
                // It's better to link to the job details page on the platform
                // rather than trying to show all details in an email.
                // We'll assume a route like 'freelancer.jobs.show' exists or will be created.
                'jobUrl' => route('freelancer.jobs.show', $this->job->id), // Placeholder, adjust if route name differs
                'freelancerName' => $this->freelancer->name,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
