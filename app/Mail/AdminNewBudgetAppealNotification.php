<?php

namespace App\Mail;

use App\Models\BudgetAppeal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminNewBudgetAppealNotification extends Mailable implements ShouldQueue
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
            subject: 'New Budget Appeal Submitted',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.admin.new-budget-appeal',
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
        $attachments = [];

        if ($this->budgetAppeal->evidence_path) {
             $attachments[] = \Illuminate\Mail\Mailables\Attachment::fromStorageDisk('private', $this->budgetAppeal->evidence_path);
        }

        return $attachments;
    }
}
