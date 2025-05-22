<?php

namespace App\Listeners;

use App\Events\JobApplicationSubmitted;
use App\Models\User;
use App\Models\Admin; // Assuming you have an Admin model or similar way to get admins
use App\Notifications\NewJobApplicationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminOfJobApplication implements ShouldQueue
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
    public function handle(JobApplicationSubmitted $event): void
    {
        $application = $event->application;
        $job = $application->job; // The job associated with the application
        $client = $job->user; // The client who posted the job

        // Notify all admins
        // If you have a specific Admin model and want to query them:
        // $admins = Admin::all(); 
        // Or, if admins are Users with a specific role:
        $admins = User::where('role', User::ROLE_ADMIN)->get();
        Notification::send($admins, new NewJobApplicationNotification($application));

        // Notify the client who posted the job, if they are not an admin themselves
        // and if they should receive such notifications.
        if ($client && $client->id !== Auth::id() && $client->role === User::ROLE_CLIENT) { // Ensure client is not the one applying (should not happen) and is a client
            // Assuming ClientProfile model has a 'receive_application_notifications' boolean field
            if ($client->clientProfile && $client->clientProfile->receive_application_notifications) {
                 $client->notify(new NewJobApplicationNotification($application));
            }
        }
    }
}
