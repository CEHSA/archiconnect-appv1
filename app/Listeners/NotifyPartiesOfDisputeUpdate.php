<?php

namespace App\Listeners;

use App\Events\DisputeUpdatedByAdmin;
use App\Notifications\DisputeStatusUpdatedDbNotification;
use App\Notifications\DisputeStatusUpdatedMailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyPartiesOfDisputeUpdate implements ShouldQueue
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
    public function handle(DisputeUpdatedByAdmin $event): void
    {
        $dispute = $event->dispute;
        $reporter = $dispute->reporter;
        $reportedUser = $dispute->reportedUser;

        // Notify the reporter
        if ($reporter) {
            $reporter->notify(new DisputeStatusUpdatedDbNotification($dispute, $reporter));
            $reporter->notify(new DisputeStatusUpdatedMailNotification($dispute, $reporter));
        }

        // Notify the reported user (if different from reporter, though unlikely in this direct model)
        if ($reportedUser && (!$reporter || $reporter->id !== $reportedUser->id)) {
            $reportedUser->notify(new DisputeStatusUpdatedDbNotification($dispute, $reportedUser));
            $reportedUser->notify(new DisputeStatusUpdatedMailNotification($dispute, $reportedUser));
        }
    }
}
