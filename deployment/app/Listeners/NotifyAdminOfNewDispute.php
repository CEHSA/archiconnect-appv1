<?php

namespace App\Listeners;

use App\Events\DisputeCreated;
use App\Models\User;
use App\Notifications\NewDisputeAdminDbNotification;
use App\Notifications\NewDisputeAdminMailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminOfNewDispute implements ShouldQueue
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
    public function handle(DisputeCreated $event): void
    {
        $admins = User::where('role', User::ROLE_ADMIN)->get();

        if ($admins->isNotEmpty()) {
            // Send DB notification to all admins
            Notification::send($admins, new NewDisputeAdminDbNotification($event->dispute));
            
            // Send Mail notification to all admins
            // Consider batching or specific admin notification preferences in a real app
            foreach ($admins as $admin) {
                $admin->notify(new NewDisputeAdminMailNotification($event->dispute));
            }
        }
    }
}
