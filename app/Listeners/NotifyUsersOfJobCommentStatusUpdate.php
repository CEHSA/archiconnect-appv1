<?php

namespace App\Listeners;

use App\Events\JobCommentStatusUpdated;
use App\Notifications\JobCommentStatusUpdatedDbNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyUsersOfJobCommentStatusUpdate implements ShouldQueue
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
    public function handle(JobCommentStatusUpdated $event): void
    {
        $comment = $event->jobComment;
        $jobAssignment = $comment->jobAssignment;

        // Notify the user who created the comment
        $commentCreator = $comment->user;
        $commentCreator->notify(new JobCommentStatusUpdatedDbNotification($comment));

        // Notify the admin who assigned the job (if different from the comment creator)
        $assigningAdmin = $jobAssignment->assignedByAdmin;
        if ($assigningAdmin && $assigningAdmin->id !== $commentCreator->id) {
            $assigningAdmin->notify(new JobCommentStatusUpdatedDbNotification($comment));
        }
    }
}
