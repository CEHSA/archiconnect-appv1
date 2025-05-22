<?php

namespace App\Listeners;

use App\Events\JobAcceptanceRequested;
use App\Models\Admin;
use App\Notifications\AdminJobAcceptanceRequestedNotification; // Will create this notification next
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminOfJobAcceptanceRequest implements ShouldQueue // Implement ShouldQueue for background processing
{
    use InteractsWithQueue; // Use InteractsWithQueue trait

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
    public function handle(JobAcceptanceRequested $event): void
    {
        // Fetch all admins to notify them
        // In a more complex system, you might have specific admins or roles to notify
        $admins = Admin::all();

        if ($admins->isNotEmpty()) {
            // Send notification to all admins
            // TODO: Create AdminJobAcceptanceRequestedNotification
            // Notification::send($admins, new AdminJobAcceptanceRequestedNotification($event->job, $event->jobAssignment, $event->freelancer));
        }
        // Optionally, log if no admins are found or if notifications are sent
        // \Log::info('Notified admins of job acceptance request for job ID: ' . $event->job->id);
    }
}
