<?php

namespace App\Mail;

use App\Models\BudgetAppeal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientBudgetAppealReviewNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The budget appeal instance.
     *
     * @var \App\Models\BudgetAppeal
     */
    public $budgetAppeal;

    /**
     * Create a new message instance.
     */
    public function __construct(BudgetAppeal $budgetAppeal)
    {
        $this->budgetAppeal = $budgetAppeal;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Budget Appeal for Your Review',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.client.budget-appeal-review',
            with: [
                'budgetAppeal' => $this->budgetAppeal,
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
        return []; // Client does not receive the evidence file directly via email
    }
}
