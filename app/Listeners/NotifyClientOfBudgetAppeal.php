<?php

namespace App\Listeners;

use App\Events\BudgetAppealForwardedToClient;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientBudgetAppealReviewNotification;
use App\Notifications\BudgetAppealForwardedToClientDbNotification; // Add this line
use Illuminate\Support\Facades\Notification as LaravelNotification; // Add this line and alias
use Illuminate\Support\Facades\Log; // Add this line

class NotifyClientOfBudgetAppeal implements ShouldQueue
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
    public function handle(BudgetAppealForwardedToClient $event): void
    {
        $budgetAppeal = $event->budgetAppeal;
        // The client is directly on the budget appeal model
        $clientUser = $budgetAppeal->client;

        if ($clientUser && $clientUser->email) {
            // Send Email Notification (already implemented)
            Mail::to($clientUser->email)->queue(new ClientBudgetAppealReviewNotification($budgetAppeal));

            // Send Database Notification
            LaravelNotification::send($clientUser, new BudgetAppealForwardedToClientDbNotification($budgetAppeal));

            Log::info("Budget appeal forwarded notification (email and DB) queued for client: {$clientUser->email} for appeal ID {$budgetAppeal->id}.");
        } elseif ($clientUser) {
            Log::warning("Client user ID {$clientUser->id} for budget appeal ID {$budgetAppeal->id} has no email address. Cannot send email notification. DB notification might still be sent if user is notifiable.");
            // Attempt DB notification even if email is missing, as it doesn't rely on email
            LaravelNotification::send($clientUser, new BudgetAppealForwardedToClientDbNotification($budgetAppeal));
            Log::info("Budget appeal forwarded DB notification attempted for client ID: {$clientUser->id} for appeal ID {$budgetAppeal->id} (email was missing).");
        } else {
            Log::error("No client user found for budget appeal ID: {$budgetAppeal->id}. Cannot send notification.");
        }
    }
}
