<?php

namespace App\Listeners;

use App\Events\FreelancerTaskProgressSubmitted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminFreelancerTaskProgressNotification;
use App\Notifications\FreelancerTaskProgressSubmittedDbNotification; // Add this line
use Illuminate\Support\Facades\Notification; // Add this line

class NotifyAdminsOfFreelancerTaskProgress implements ShouldQueue // Implement ShouldQueue
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
    public function handle(FreelancerTaskProgressSubmitted $event): void
    {
        $taskProgress = $event->taskProgress;

        // Get all admin users
        $admins = User::where('role', User::ROLE_ADMIN)->get();

        // Send notification to each admin
        foreach ($admins as $admin) {
            // Send Email Notification
            Mail::to($admin->email)->send(new AdminFreelancerTaskProgressNotification($taskProgress));

            // Send Database Notification
            Notification::send($admin, new FreelancerTaskProgressSubmittedDbNotification($taskProgress));
        }
    }
}
