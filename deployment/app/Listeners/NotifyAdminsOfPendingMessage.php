<?php

namespace App\Listeners;

use App\Events\FreelancerMessageCreated;
use App\Models\User; // Import the User model
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminPendingMessageNotification;
use App\Notifications\FreelancerMessageCreatedDbNotification; // Add this line
use Illuminate\Support\Facades\Notification; // Add this line
use Illuminate\Support\Facades\Log; // Add this line

class NotifyAdminsOfPendingMessage implements ShouldQueue
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
    public function handle(FreelancerMessageCreated $event): void
    {
        // Get all users with the 'admin' role
        $admins = User::where('role', User::ROLE_ADMIN)->get();

        if ($admins->isEmpty()) {
            Log::info("No admins found to notify about new message ID: {$event->message->id}.");
            return;
        }

        // Send notification to each admin
        foreach ($admins as $admin) {
            if ($admin->email) {
                // Send Email Notification
                Mail::to($admin->email)->queue(new AdminPendingMessageNotification($event->message));

                // Send Database Notification
                Notification::send($admin, new FreelancerMessageCreatedDbNotification($event->message));

                Log::info("New message notification (email and DB) queued for admin: {$admin->email} for message ID {$event->message->id}.");
            } else {
                Log::warning("Admin user ID {$admin->id} has no email address. Cannot send new message notification.");
            }
        }
    }
}
