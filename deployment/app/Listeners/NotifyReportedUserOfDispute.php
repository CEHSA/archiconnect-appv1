<?php

namespace App\Listeners;

use App\Events\DisputeCreated;
use App\Notifications\UserReportedInDisputeDbNotification;
use App\Notifications\UserReportedInDisputeMailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyReportedUserOfDispute implements ShouldQueue
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
        $reportedUser = $event->dispute->reportedUser;

        if ($reportedUser) {
            // Send DB Notification
            $reportedUser->notify(new UserReportedInDisputeDbNotification($event->dispute));
            
            // Send Mail Notification
            $reportedUser->notify(new UserReportedInDisputeMailNotification($event->dispute));
        }
    }
}
