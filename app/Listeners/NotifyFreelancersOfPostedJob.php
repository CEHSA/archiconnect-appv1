<?php

namespace App\Listeners;

use App\Events\JobPostedToFreelancers;
use App\Models\User;
use App\Notifications\NewJobPostedToFreelancerNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyFreelancersOfPostedJob implements ShouldQueue
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
    public function handle(JobPostedToFreelancers $event): void
    {
        $job = $event->job;
        $freelancerUsers = User::whereIn('id', $event->freelancerIds)->get();

        foreach ($freelancerUsers as $freelancerUser) {
            // Ensure freelancer wants notifications and has a profile
            if ($freelancerUser->freelancerProfile && $freelancerUser->freelancerProfile->receive_new_job_notifications) {
                $freelancerUser->notify(new NewJobPostedToFreelancerNotification($job));
            }
        }
    }
}
