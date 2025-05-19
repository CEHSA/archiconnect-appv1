<?php

namespace App\Notifications;

use App\Models\BudgetAppeal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BudgetAppealDecisionMadeDbNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public BudgetAppeal $budgetAppeal;

    /**
     * Create a new notification instance.
     */
    public function __construct(BudgetAppeal $budgetAppeal)
    {
        $this->budgetAppeal = $budgetAppeal;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $jobAssignment = $this->budgetAppeal->jobAssignment;
        $jobTitle = $jobAssignment->job->title ?? 'N/A';
        $clientName = $this->budgetAppeal->client->name ?? 'The client';
        $status = $this->budgetAppeal->status; // e.g., 'approved_by_client', 'rejected_by_client'

        $decision = match ($status) {
            BudgetAppeal::STATUS_APPROVED_BY_CLIENT => 'approved',
            BudgetAppeal::STATUS_REJECTED_BY_CLIENT => 'rejected',
            default => 'made a decision on',
        };

        return [
            'title' => 'Budget Appeal Decision Made',
            'message' => "{$clientName} has {$decision} your budget appeal for job '{$jobTitle}'.",
            'budget_appeal_id' => $this->budgetAppeal->id,
            'job_assignment_id' => $jobAssignment->id,
            'job_id' => $jobAssignment->job_id,
            'status' => $status,
            // Freelancers don't have a dedicated page to view appeals,
            // they see the status on their assignment page.
            // So, link to the assignment.
            'url' => route('freelancer.assignments.show', $jobAssignment->id)
        ];
    }
}
