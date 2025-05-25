<?php

namespace App\Listeners;

use App\Events\JobCompleted;
use App\Notifications\FreelancerJobCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyFreelancerOfJobCompletion implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(JobCompleted $event)
    {
        $freelancer = $event->job->freelancer;
        $freelancer->notify(new FreelancerJobCompletedNotification(
            $event->job,
            $event->completionStatus,
            $event->notes
        ));
    }
}
