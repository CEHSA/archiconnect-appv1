<?php

namespace App\Listeners;

use App\Events\FreelancerWorkSubmitted;
use App\Mail\AdminWorkSubmittedNotification;
use App\Models\User;
use App\Notifications\FreelancerWorkSubmittedDbNotification; // Add this line
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification; // Add this line

class NotifyAdminOfWorkSubmission implements ShouldQueue
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
    public function handle(FreelancerWorkSubmitted $event): void
    {
        // Get all admin users
        $admins = User::where('role', User::ROLE_ADMIN)->get();

        if ($admins->isNotEmpty()) {
            // Eager load necessary relationships for the mail if not already loaded
            $event->workSubmission->loadMissing(['freelancer', 'jobAssignment.job', 'jobAssignment.assignedByAdmin']);
            
            // Determine which admin to notify.
            // Option 1: Notify the admin who assigned the job, if available.
            $assigningAdmin = $event->workSubmission->jobAssignment->assignedByAdmin;

            if ($assigningAdmin) {
                Mail::to($assigningAdmin->email)
                    ->send(new AdminWorkSubmittedNotification($event->workSubmission));

                // Send database notification to the assigning admin
                Notification::send($assigningAdmin, new FreelancerWorkSubmittedDbNotification($event->workSubmission)); // Add this line
            } else {
                // Option 2: Fallback to notifying all admins if the assigning admin is not found (or if preferred)
                // Or, you might have a specific admin email in config for general notifications.
                // For now, let's notify all admins if specific one isn't found.
                foreach ($admins as $admin) {
                    Mail::to($admin->email)
                        ->send(new AdminWorkSubmittedNotification($event->workSubmission));

                    // Send database notification to all admins
                    Notification::send($admin, new FreelancerWorkSubmittedDbNotification($event->workSubmission)); // Add this line
                }
            }
        }
    }
}
