<?php

namespace App\Mail;

use App\Models\WorkSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class FreelancerSubmissionReviewedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $workSubmission;

    /**
     * Create a new message instance.
     */
    public function __construct(WorkSubmission $workSubmission)
    {
        $this->workSubmission = $workSubmission;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $status = ucfirst($this->workSubmission->status);
        $jobTitle = $this->workSubmission->jobAssignment->job->title;

        return new Envelope(
            subject: "Your Work Submission for '{$jobTitle}' has been Reviewed ({$status})",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.freelancer.submission-reviewed',
            with: [
                'workSubmission' => $this->workSubmission,
                'jobTitle' => $this->workSubmission->jobAssignment->job->title,
                'adminRemarks' => $this->workSubmission->admin_remarks,
                'status' => $this->workSubmission->status,
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
