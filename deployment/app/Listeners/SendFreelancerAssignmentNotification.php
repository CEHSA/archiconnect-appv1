<?php

namespace App\Listeners;

use App\Events\JobAssigned;
use App\Mail\FreelancerAssignedToJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Notifications\JobAssignedDbNotification; // Add this line
use Illuminate\Support\Facades\Notification; // Add this line
use Illuminate\Support\Facades\Log; // Add this line

class SendFreelancerAssignmentNotification implements ShouldQueue
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
    public function handle(JobAssigned $event): void
    {
        $jobAssignment = $event->assignment;
        $freelancerUser = $jobAssignment->freelancer; // This is the User model instance for the freelancer

        if ($freelancerUser && $freelancerUser->email) {
            // Send Email Notification
            Mail::to($freelancerUser->email)
                ->send(new FreelancerAssignedToJob($jobAssignment));

            // Send Database Notification
            Notification::send($freelancerUser, new JobAssignedDbNotification($jobAssignment));

            Log::info("Job assignment notification (email and DB) sent to freelancer: {$freelancerUser->email} for assignment ID {$jobAssignment->id}.");

        } else {
            Log::error("Could not send assignment notification: Freelancer user or email missing for assignment ID {$jobAssignment->id}");
        }
    }
}
