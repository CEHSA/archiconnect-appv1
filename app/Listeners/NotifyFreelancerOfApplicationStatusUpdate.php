<?php

namespace App\Listeners;

use App\Events\JobApplicationStatusUpdated;
use App\Notifications\JobApplicationStatusUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyFreelancerOfApplicationStatusUpdate implements ShouldQueue
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
    public function handle(JobApplicationStatusUpdated $event): void
    {
        $application = $event->application;
        $freelancer = $application->freelancer; // User model of the freelancer

        if ($freelancer) {
            // Check if freelancer wants notifications for application status updates
            // This preference might be on FreelancerProfile or User model
            // For example: if ($freelancer->freelancerProfile && $freelancer->freelancerProfile->receive_application_status_notifications)
            
            $freelancer->notify(new JobApplicationStatusUpdatedNotification($application, $event->oldStatus));
        }
    }
}
