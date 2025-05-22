<?php

namespace App\Listeners;

use App\Events\BudgetAppealCreated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminNewBudgetAppealNotification;
use App\Models\User;
use App\Notifications\BudgetAppealCreatedDbNotification; // Add this line
use Illuminate\Support\Facades\Notification as LaravelNotification; // Add this line and alias
use Illuminate\Support\Facades\Log; // Add this line

class NotifyAdminsOfNewBudgetAppeal implements ShouldQueue
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
    public function handle(BudgetAppealCreated $event): void
    {
        $budgetAppeal = $event->budgetAppeal;
        $admins = User::where('role', User::ROLE_ADMIN)->get();

        if ($admins->isEmpty()) {
            Log::info("No admins found to notify about new budget appeal ID: {$budgetAppeal->id}.");
            return;
        }

        foreach ($admins as $admin) {
            if ($admin->email) {
                // Send Email Notification (already implemented)
                Mail::to($admin->email)->queue(new AdminNewBudgetAppealNotification($budgetAppeal));

                // Send Database Notification
                LaravelNotification::send($admin, new BudgetAppealCreatedDbNotification($budgetAppeal));

                Log::info("New budget appeal notification (email and DB) queued for admin: {$admin->email} for appeal ID {$budgetAppeal->id}.");
            } else {
                Log::warning("Admin user ID {$admin->id} has no email address. Cannot send new budget appeal notification.");
            }
        }
    }
}
