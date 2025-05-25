<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\BudgetAppeal;
use App\Models\JobAssignment;

class BudgetAppealCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public BudgetAppeal $appeal,
        public JobAssignment $jobAssignment
    ) {}

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("New Budget Appeal for Job: {$this->jobAssignment->job->title}")
            ->line("A new budget appeal has been submitted by freelancer {$this->appeal->freelancer->name}.")
            ->line("Reason: {$this->appeal->reason}")
            ->line("Requested Amount: {$this->appeal->requested_budget}")
            ->action('Review Appeal', route('admin.budget-appeals.show', $this->appeal));
    }

    public function toArray($notifiable)
    {
        return [
            'appeal_id' => $this->appeal->id,
            'job_id' => $this->jobAssignment->job->id,
            'job_title' => $this->jobAssignment->job->title,
            'freelancer_name' => $this->appeal->freelancer->name,
            'reason' => $this->appeal->reason,
            'requested_amount' => $this->appeal->requested_budget,
            'url' => route('admin.budget-appeals.show', $this->appeal)
        ];
    }
}
