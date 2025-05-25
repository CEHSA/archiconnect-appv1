<?php

namespace App\Listeners;

use App\Events\BudgetAppealCreated;
use App\Models\User;
use App\Notifications\BudgetAppealCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminOfBudgetAppeal implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(BudgetAppealCreated $event): void
    {
        $budgetAppeal = $event->budgetAppeal;
        $jobAssignment = $budgetAppeal->jobAssignment; // BudgetAppeal has jobAssignment() relationship

        // Notify all admins
        $admins = User::where('role', User::ROLE_ADMIN)->get();
        Notification::send($admins, new BudgetAppealCreatedNotification($budgetAppeal, $jobAssignment));
    }
}
