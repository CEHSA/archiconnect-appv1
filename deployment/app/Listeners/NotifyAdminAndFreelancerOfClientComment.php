<?php

namespace App\Listeners;

use App\Events\JobCommentCreated;
use App\Models\Admin;
use App\Models\User;
use App\Models\WorkSubmission;
use App\Notifications\ClientCommentNotificationToAdmin; // Placeholder
use App\Notifications\ClientCommentNotificationToFreelancer; // Placeholder
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminAndFreelancerOfClientComment implements ShouldQueue
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

        // Check if the comment is from a Client and associated with a WorkSubmission
        if ($comment->user_type === 'App\\Models\\User' && $comment->user && $comment->user->isClient() && $comment->work_submission_id) {
            $workSubmission = WorkSubmission::with(['jobAssignment.freelancer.user', 'jobAssignment.job.client.user'])->find($comment->work_submission_id);

            if (!$workSubmission || !$workSubmission->jobAssignment || !$workSubmission->jobAssignment->freelancer) {
                // Log or handle missing critical data
                return;
            }

            $freelancerUser = $workSubmission->jobAssignment->freelancer->user;
            $admins = Admin::all(); // Assuming Admins should be notified. Adjust if specific admins.

            // Notify the Freelancer
            if ($freelancerUser) {
                // Notification::send($freelancerUser, new ClientCommentNotificationToFreelancer($comment, $workSubmission));
                $freelancerUser->notify(new ClientCommentNotificationToFreelancer($comment, $workSubmission));
            }

            // Notify Admins
            if ($admins->isNotEmpty()) {
                // Notification::send($admins, new ClientCommentNotificationToAdmin($comment, $workSubmission));
                foreach ($admins as $admin) {
                    $admin->notify(new ClientCommentNotificationToAdmin($comment, $workSubmission));
                }
            }
        }
    }
}
