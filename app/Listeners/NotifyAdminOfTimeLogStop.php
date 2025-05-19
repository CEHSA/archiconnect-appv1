<?php

namespace App\Listeners;

use App\Events\FreelancerTimeLogStopped;
use App\Models\Admin;
use App\Notifications\TimeLogStoppedNotification; // Will create this notification
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminOfTimeLogStop implements ShouldQueue
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
    public function handle(FreelancerTimeLogStopped $event): void
    {
        $admins = Admin::all(); // Or get specific admins based on settings/roles
        Notification::send($admins, new TimeLogStoppedNotification($event->timeLog));
    }
}
