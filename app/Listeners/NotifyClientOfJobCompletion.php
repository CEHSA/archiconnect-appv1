<?php

namespace App\Listeners;

use App\Events\JobCompleted;
use App\Notifications\JobCompletedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotifyClientOfJobCompletion implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(JobCompleted $event)
    {
        $client = $event->job->client;
        $client->notify(new JobCompletedNotification(
            $event->job,
            $event->completionStatus,
            $event->notes
        ));
    }
}
