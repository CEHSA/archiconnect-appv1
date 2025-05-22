<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use App\Models\Message; // Import the Message model
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminPendingMessageNotification extends Mailable implements ShouldQueue // Implement ShouldQueue
{
    use Queueable, SerializesModels;

    public $message; // Public property to hold the message

    /**
     * Create a new message instance.
     */
    public function __construct(Message $message) // Accept Message instance
    {
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Pending Message for Review', // Updated subject
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.pending-message',
            with: [
                'message' => $this->message, // Pass the message to the view
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
