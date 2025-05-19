<?php

namespace App\Listeners;

use App\Events\BudgetAppealDecisionMade;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\FreelancerBudgetAppealDecisionNotification;
use App\Notifications\BudgetAppealDecisionMadeDbNotification; // Add this line
use Illuminate\Support\Facades\Notification as LaravelNotification; // Add this line and alias
use Illuminate\Support\Facades\Log; // Add this line

class NotifyFreelancerOfBudgetAppealDecision implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BudgetAppealDecisionMade $event): void
    {
        $budgetAppeal = $event->budgetAppeal;
        $freelancer = $budgetAppeal->freelancer;

        if ($freelancer && $freelancer->email) {
            // Send Email Notification (already implemented)
            Mail::to($freelancer->email)->queue(new FreelancerBudgetAppealDecisionNotification($budgetAppeal));

            // Send Database Notification
            LaravelNotification::send($freelancer, new BudgetAppealDecisionMadeDbNotification($budgetAppeal));

            Log::info("Budget appeal decision notification (email and DB) queued for freelancer: {$freelancer->email} for appeal ID {$budgetAppeal->id}.");
        } elseif ($freelancer) {
            Log::warning("Freelancer user ID {$freelancer->id} for budget appeal ID {$budgetAppeal->id} has no email address. Cannot send email notification. DB notification might still be sent if user is notifiable.");
            // Attempt DB notification even if email is missing
            LaravelNotification::send($freelancer, new BudgetAppealDecisionMadeDbNotification($budgetAppeal));
            Log::info("Budget appeal decision DB notification attempted for freelancer ID: {$freelancer->id} for appeal ID {$budgetAppeal->id} (email was missing).");
        } else {
            Log::error("No freelancer found for budget appeal ID: {$budgetAppeal->id}. Cannot send notification.");
        }
    }
}
