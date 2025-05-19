<?php

namespace App\Mail;

use App\Models\JobAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FreelancerAssignedToJob extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public JobAssignment $assignment;

    /**
     * Create a new message instance.
     */
    public function __construct(JobAssignment $assignment)
    {
        $this->assignment = $assignment;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'You have been assigned to a new job: ' . $this->assignment->job->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.jobs.freelancer-assigned',
            with: [
                'jobTitle' => $this->assignment->job->title,
                'jobDescription' => $this->assignment->job->description,
                'adminRemarks' => $this->assignment->admin_remarks,
                // It's good practice to provide a link to view the job or assignment details
                // For now, let's assume a generic dashboard link or a placeholder
                'jobUrl' => route('freelancer.dashboard'), // Placeholder, ideally link to the specific job/assignment
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
