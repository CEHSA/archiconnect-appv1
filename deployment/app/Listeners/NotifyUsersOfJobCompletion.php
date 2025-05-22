<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Events\JobCompleted;
use Illuminate\Support\Facades\Mail;
use App\Mail\JobCompletedNotification;
use App\Notifications\JobCompletedDbNotification; // Add this line
use Illuminate\Support\Facades\Notification; // Add this line

class NotifyUsersOfJobCompletion implements ShouldQueue
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
    public function handle(JobCompleted $event): void // Type-hint the event
    {
        $jobAssignment = $event->jobAssignment;
        $client = $jobAssignment->job->client; // Assuming Job model has a client relationship
        $freelancer = $jobAssignment->freelancer; // Assuming JobAssignment model has a freelancer relationship

        // Send notification to client
        if ($client && $client->user) { // Ensure client and client->user exist
            Mail::to($client->user->email)->send(new JobCompletedNotification($jobAssignment));
            Notification::send($client->user, new JobCompletedDbNotification($jobAssignment, $client->user));
        }

        // Send notification to freelancer
        if ($freelancer && $freelancer->user) { // Ensure freelancer and freelancer->user exist
            Mail::to($freelancer->user->email)->send(new JobCompletedNotification($jobAssignment));
            Notification::send($freelancer->user, new JobCompletedDbNotification($jobAssignment, $freelancer->user));
        }
    }
}
