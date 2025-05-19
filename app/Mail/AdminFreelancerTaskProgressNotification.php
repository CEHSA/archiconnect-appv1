<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\TaskProgress; // Add this line

class AdminFreelancerTaskProgressNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public TaskProgress $taskProgress) // Modify constructor
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Task Progress Update Submitted', // Update subject
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.freelancer-task-progress',
            with: [ // Pass data to the view
                'taskProgress' => $this->taskProgress,
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
        // Attach the uploaded file if it exists
        if ($this->taskProgress->file_path) {
            return [
                \Illuminate\Mail\Mailables\Attachment::fromStorageDisk('private', $this->taskProgress->file_path)
                    ->as($this->taskProgress->original_filename ?? 'progress_file') // Use original filename if available
                    ->withMime('application/octet-stream'), // Generic mime type
            ];
        }

        return [];
    }
}
