<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\JobAssignment;

class AdminFreelancerResponseNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public JobAssignment $jobAssignment;

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
            subject: 'Freelancer Responded to Job Assignment: ' . $this->jobAssignment->job->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.freelancer-response',
            with: [
                'assignment' => $this->jobAssignment,
                'freelancerName' => $this->jobAssignment->freelancer->name,
                'jobTitle' => $this->jobAssignment->job->title,
                'status' => $this->jobAssignment->status,
                'assignmentUrl' => route('admin.assignments.show', $this->jobAssignment->id),
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
