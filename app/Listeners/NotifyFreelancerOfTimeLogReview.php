<?php

namespace App\Listeners;

use App\Events\TimeLogReviewedByAdmin;
use App\Notifications\TimeLogReviewedNotification; // Will create this notification
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyFreelancerOfTimeLogReview implements ShouldQueue
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
    public function handle(TimeLogReviewedByAdmin $event): void
    {
        Notification::send($event->timeLog->freelancer, new TimeLogReviewedNotification($event->timeLog));
    }
}
