<?php

namespace App\Mail;

use App\Models\WorkSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminWorkSubmittedNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public WorkSubmission $workSubmission;

    /**
     * Create a new message instance.
     */
    public function __construct(WorkSubmission $workSubmission)
    {
        $this->workSubmission = $workSubmission->loadMissing(['freelancer', 'jobAssignment.job']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Work Submitted: ' . $this->workSubmission->jobAssignment->job->title,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.work-submitted',
            with: [
                'submissionTitle' => $this->workSubmission->title,
                'jobTitle' => $this->workSubmission->jobAssignment->job->title,
                'freelancerName' => $this->workSubmission->freelancer->name,
                'submissionUrl' => route('admin.work-submissions.show', $this->workSubmission->id),
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
