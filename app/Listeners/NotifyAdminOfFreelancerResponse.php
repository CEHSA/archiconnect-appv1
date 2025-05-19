<?php

namespace App\Listeners;

use App\Events\FreelancerRespondedToAssignment;
use App\Mail\AdminFreelancerResponseNotification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Notifications\FreelancerRespondedToAssignmentDbNotification; // Add this line
use Illuminate\Support\Facades\Notification as LaravelNotification; // Alias to avoid conflict

class NotifyAdminOfFreelancerResponse implements ShouldQueue
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
    public function handle(FreelancerRespondedToAssignment $event): void
    {
        $jobAssignment = $event->jobAssignment;
        $adminUser = $jobAssignment->assignedByAdmin;

        if ($adminUser && $adminUser->email) {
            // Send Email Notification
            Mail::to($adminUser->email)
                ->send(new AdminFreelancerResponseNotification($jobAssignment));
            // Send Database Notification
            LaravelNotification::send($adminUser, new FreelancerRespondedToAssignmentDbNotification($jobAssignment));

            Log::info("Admin freelancer response notification sent to specific admin: {$adminUser->email} for assignment ID {$jobAssignment->id}.");
        } else {
            // Fallback: Send to all users with 'admin' role
            $adminUsers = User::where('role', User::ROLE_ADMIN)->get(); // Use constant for role
            if ($adminUsers->isNotEmpty()) {
                foreach ($adminUsers as $admin) {
                    if ($admin->email) {
                        // Send Email Notification
                        Mail::to($admin->email)
                            ->send(new AdminFreelancerResponseNotification($jobAssignment));
                        // Send Database Notification
                        LaravelNotification::send($admin, new FreelancerRespondedToAssignmentDbNotification($jobAssignment));
                    }
                }
                Log::info("Admin freelancer response notification sent to all admins for assignment ID {$jobAssignment->id} as specific assigning admin was not found or had no email.");
            } else {
                Log::warning("No admin users found to notify for freelancer response on assignment ID {$jobAssignment->id}.");
            }
        }
    }
}
