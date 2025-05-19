<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\JobAssignment; // Add this line

class JobCompletedNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The job assignment instance.
     *
     * @var \App\Models\JobAssignment
     */
    public $jobAssignment;

    /**
     * Create a new message instance.
     */
    public function __construct(JobAssignment $jobAssignment)
    {
        $this->jobAssignment = $jobAssignment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Job Completed: ' . $this->jobAssignment->job->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.jobs.completed',
            with: [
                'jobAssignment' => $this->jobAssignment,
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
