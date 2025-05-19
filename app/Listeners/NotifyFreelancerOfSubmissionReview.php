<?php

namespace App\Listeners;

use App\Events\WorkSubmissionReviewedByAdmin;
use App\Mail\FreelancerSubmissionReviewedNotification;
use App\Notifications\WorkSubmissionReviewedByAdminDbNotification; // Add this line
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification; // Add this line

class NotifyFreelancerOfSubmissionReview implements ShouldQueue
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
    public function handle(WorkSubmissionReviewedByAdmin $event): void
    {
        $workSubmission = $event->workSubmission;
        $freelancer = $workSubmission->jobAssignment->freelancer;

        Mail::to($freelancer->email)->send(new FreelancerSubmissionReviewedNotification($workSubmission));

        // Send database notification to the freelancer
        $freelancer->user->notify(new WorkSubmissionReviewedByAdminDbNotification($workSubmission)); // Add this line
    }
}
