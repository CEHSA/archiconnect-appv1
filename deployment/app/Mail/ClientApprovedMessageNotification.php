<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

use App\Models\Message;

class ClientApprovedMessageNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Message $message;

    /**
     * Create a new message instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'A Message Regarding Your Job Has Been Approved',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.messages.client-approved',
            with: [
                'messageContent' => $this->message->content,
                'freelancerName' => $this->message->user->name, // Assuming the message sender is the freelancer
                'jobTitle' => $this->message->conversation->job->title,
                'viewConversationUrl' => route('client.dashboard'), // Placeholder, ideally link to the specific conversation/job
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
