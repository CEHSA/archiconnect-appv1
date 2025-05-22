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
        try {
            $comment = $event->jobComment->loadMissing([
                'user',
                'job.jobAssignment.assignedByAdmin',
                'job.client.user',
                'job.jobAssignment.freelancer.user'
            ]);
            
            if (!$comment->job) {
                \Illuminate\Support\Facades\Log::warning('JobCommentCreated event for comment ID ' . $comment->id . ' has no associated job. Skipping notifications.');
                return;
            }

            $jobAssignment = $comment->job->jobAssignment;
            
            if (!$jobAssignment) {
                \Illuminate\Support\Facades\Log::warning('Job ID ' . $comment->job_id . ' has no associated job assignment. Skipping notifications.');
                return;
            }

            // Notify the admin who assigned the job
            $assigningAdmin = $jobAssignment->assignedByAdmin;
            if ($assigningAdmin && $assigningAdmin->id !== $comment->user_id) {
                try {
                    $assigningAdmin->notify(new NewJobCommentDbNotification($comment));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to notify assigning admin: ' . $e->getMessage());
                }
            }

            // Notify the client
            $clientUser = $comment->job->client->user ?? null;
            if ($clientUser && $clientUser->id !== $comment->user_id) {
                try {
                    $clientUser->notify(new NewJobCommentDbNotification($comment));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to notify client: ' . $e->getMessage());
                }
            }

            // Notify the freelancer
            $freelancerUser = $jobAssignment->freelancer->user ?? null;
            if ($freelancerUser && $freelancerUser->id !== $comment->user_id) {
                try {
                    $freelancerUser->notify(new NewJobCommentDbNotification($comment));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to notify freelancer: ' . $e->getMessage());
                }
            }

            // Notify other admins
            try {
                $admins = User::where('role', 'admin')->get();
                foreach ($admins as $admin) {
                    // Skip if this admin is the commenter or assigning admin
                    if ($admin->id === $comment->user_id || 
                        ($assigningAdmin && $admin->id === $assigningAdmin->id)) {
                        continue;
                    }
                    
                    try {
                        $admin->notify(new NewJobCommentDbNotification($comment));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error('Failed to notify admin ' . $admin->id . ': ' . $e->getMessage());
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Failed to fetch or process admin notifications: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error processing JobCommentCreated event: ' . $e->getMessage());
            return;
        }
    }
}
