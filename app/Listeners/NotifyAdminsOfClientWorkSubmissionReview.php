<?php

namespace App\Listeners;

use App\Events\ClientWorkSubmissionReviewed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ClientWorkSubmissionReviewedNotification; // This is for Email
use App\Notifications\ClientWorkSubmissionReviewedDbNotification; // Add this for DB
use Illuminate\Support\Facades\Log; // Add this line

class NotifyAdminsOfClientWorkSubmissionReview implements ShouldQueue
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
    public function handle(ClientWorkSubmissionReviewed $event): void
    {
        $admins = User::where('role', User::ROLE_ADMIN)->get();
        $workSubmission = $event->workSubmission;

        if ($admins->isEmpty()) {
            Log::info("No admins found to notify about client review for work submission ID: {$workSubmission->id}.");
            return;
        }

        foreach ($admins as $admin) {
            if ($admin->email) {
                // Send Email Notification (already implemented)
                Notification::send($admin, new ClientWorkSubmissionReviewedNotification($workSubmission));

                // Send Database Notification
                Notification::send($admin, new ClientWorkSubmissionReviewedDbNotification($workSubmission));

                Log::info("Client work submission review notification (email and DB) sent to admin: {$admin->email} for work submission ID {$workSubmission->id}.");
            } else {
                Log::warning("Admin user ID {$admin->id} has no email address. Cannot send client work submission review notification.");
            }
        }
    }
}
