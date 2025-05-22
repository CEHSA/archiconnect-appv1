<?php

namespace App\Listeners;

use App\Events\AdminJobPosted;
use App\Models\User;
use App\Mail\NewJobAvailableNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Notifications\AdminJobPostedDbNotification; // Add this line
use Illuminate\Support\Facades\Notification; // Add this line
use Illuminate\Support\Facades\Log; // Add this line

class NotifyFreelancersAboutNewJob implements ShouldQueue
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
    public function handle(AdminJobPosted $event): void
    {
        $freelancers = User::where('role', User::ROLE_FREELANCER)
                           ->whereHas('freelancerProfile', function ($query) {
                               $query->where('receive_new_job_notifications', true);
                           })
                           ->with('freelancerProfile') // Eager load the profile
                           ->get();

        if ($freelancers->isEmpty()) {
            Log::info("No freelancers found to notify about new job ID: {$event->job->id} (either no freelancers or none opted-in).");
            return;
        }

        foreach ($freelancers as $freelancer) {
            // Send Email Notification
            Mail::to($freelancer->email)
                ->queue(new NewJobAvailableNotification($event->job, $freelancer));

            // Send Database Notification
            Notification::send($freelancer, new AdminJobPostedDbNotification($event->job));

            Log::info("New job notification (email and DB) queued for freelancer: {$freelancer->email} for job ID {$event->job->id}.");
        }
    }
}
