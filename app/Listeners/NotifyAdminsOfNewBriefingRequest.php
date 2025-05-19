<?php

namespace App\Listeners;

use App\Events\BriefingRequestCreated;
use App\Models\User;
use App\Notifications\NewBriefingRequestDbNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyAdminsOfNewBriefingRequest implements ShouldQueue
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
    public function handle(BriefingRequestCreated $event): void
    {
        $admins = User::where('role', 'admin')->get();

        Notification::send($admins, new NewBriefingRequestDbNotification($event->briefingRequest));
    }
}
