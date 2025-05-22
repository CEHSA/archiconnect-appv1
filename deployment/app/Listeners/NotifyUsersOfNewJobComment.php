<?php

namespace App\Listeners;

use App\Events\JobCommentCreated;
use App\Models\User;
use App\Notifications\NewJobCommentDbNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyUsersOfNewJobComment implements ShouldQueue
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
    public function handle(JobCommentCreated $event): void
    {
        $comment = $event->jobComment;
        $jobAssignment = $comment->jobAssignment;
        $job = $jobAssignment->job;

        // Notify the admin who assigned the job
        $assigningAdmin = $jobAssignment->assignedByAdmin;
        if ($assigningAdmin) {
            $assigningAdmin->notify(new NewJobCommentDbNotification($comment));
        }

        // Notify the client
        $client = $job->client->user;
        if ($client->id !== $comment->user_id) { // Don't notify the user who created the comment
             $client->notify(new NewJobCommentDbNotification($comment));
        }


        // Notify the freelancer
        $freelancer = $jobAssignment->freelancer->user;
         if ($freelancer->id !== $comment->user_id) { // Don't notify the user who created the comment
            $freelancer->notify(new NewJobCommentDbNotification($comment));
        }

        // Notify any other admins (if the assigning admin wasn't found or if we want to notify all)
        $admins = User::where('role', 'admin')->get();
         foreach ($admins as $admin) {
             if ($assigningAdmin && $admin->id === $assigningAdmin->id) {
                 continue; // Already notified the assigning admin
             }
             if ($admin->id !== $comment->user_id) { // Don't notify the user who created the comment
                 $admin->notify(new NewJobCommentDbNotification($comment));
             }
         }
    }
}
