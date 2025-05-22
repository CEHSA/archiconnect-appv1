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
        $comment = $event->jobComment->loadMissing(['user', 'jobAssignment.job.client.user', 'jobAssignment.assignedByAdmin', 'jobAssignment.freelancer.user']);
        
        $jobAssignment = $comment->jobAssignment;

        // If the comment is not associated with a specific job assignment,
        // we might not need to send these assignment-specific notifications.
        // Or, handle general job comments differently if that's a feature.
        if (!$jobAssignment) {
            // Log this situation or handle as per business logic for general comments.
            // For now, we'll simply return to prevent errors.
            \Illuminate\Support\Facades\Log::info('JobCommentCreated event for comment ID ' . $comment->id . ' has no associated jobAssignment. Skipping notifications.');
            return;
        }

        $job = $jobAssignment->job;

        if (!$job) {
            // This case should ideally not happen if a jobAssignment exists, but as a safeguard:
            \Illuminate\Support\Facades\Log::warning('JobCommentCreated event for comment ID ' . $comment->id . ' has a jobAssignment (ID ' . $jobAssignment->id . ') but no associated job. Skipping notifications.');
            return;
        }

        // Notify the admin who assigned the job
        $assigningAdmin = $jobAssignment->assignedByAdmin;
        if ($assigningAdmin && $assigningAdmin->id !== $comment->user_id) {
            $assigningAdmin->notify(new NewJobCommentDbNotification($comment));
        }

        // Notify the client
        if ($job->client && $job->client->user && $job->client->user->id !== $comment->user_id) { 
             $job->client->user->notify(new NewJobCommentDbNotification($comment));
        }

        // Notify the freelancer
        if ($jobAssignment->freelancer && $jobAssignment->freelancer->user && $jobAssignment->freelancer->user->id !== $comment->user_id) { 
            $jobAssignment->freelancer->user->notify(new NewJobCommentDbNotification($comment));
        }

        // Notify any other admins
        $admins = User::where('role', 'admin')->get();
         foreach ($admins as $admin) {
             // Skip if this admin is the one who made the comment
             if ($admin->id === $comment->user_id) {
                 continue;
             }
             // Skip if this admin is the assigning admin and was already notified (or would have been)
             if ($assigningAdmin && $admin->id === $assigningAdmin->id) {
                 continue; 
             }
             $admin->notify(new NewJobCommentDbNotification($comment));
         }
    }
}
