<?php

namespace App\Mail;

use App\Models\BudgetAppeal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str; // Import Str facade

class FreelancerBudgetAppealDecisionNotification extends Mailable implements ShouldQueue
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
        $subject = 'Budget Appeal ' . Str::title(str_replace('_', ' ', $this->budgetAppeal->status));
        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.freelancer.budget-appeal-decision',
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
        return [];
    }
}
