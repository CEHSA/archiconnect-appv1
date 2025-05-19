<?php

namespace App\Notifications;

use App\Models\BudgetAppeal;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BudgetAppealCreatedDbNotification extends Notification implements ShouldQueue
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
        $freelancerName = $this->budgetAppeal->freelancer->name ?? 'A freelancer';

        return [
            'title' => 'New Budget Appeal Submitted',
            'message' => "{$freelancerName} has submitted a new budget appeal for job '{$jobTitle}'.",
            'budget_appeal_id' => $this->budgetAppeal->id,
            'job_assignment_id' => $jobAssignment->id,
            'job_id' => $jobAssignment->job_id,
            'url' => route('admin.budget-appeals.show', $this->budgetAppeal->id) // Link to admin view of the appeal
        ];
    }
}
