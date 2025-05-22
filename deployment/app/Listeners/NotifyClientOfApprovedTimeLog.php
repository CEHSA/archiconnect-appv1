<?php

namespace App\Listeners;

use App\Events\ClientNotificationForApprovedTimeLog;
use App\Notifications\ApprovedTimeLogNotificationToClient; // Will create this notification
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyClientOfApprovedTimeLog implements ShouldQueue
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
    public function handle(ClientNotificationForApprovedTimeLog $event): void
    {
        $client = $event->timeLog->assignmentTask->jobAssignment->job->client;
        if ($client) {
            Notification::send($client, new ApprovedTimeLogNotificationToClient($event->timeLog));
        }
    }
}
