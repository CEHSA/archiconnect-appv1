<?php

namespace App\Listeners;

use App\Events\AdminMessageSent;
use App\Models\User;
use App\Notifications\NewMessageFromAdminNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyParticipantsOfAdminMessage implements ShouldQueue
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
    public function handle(AdminMessageSent $event): void
    {
        $message = $event->message;
        $conversation = $message->conversation;
        $sender = $message->user; // This is the admin who sent the message

        // Get all participants of the conversation except the sender admin
        $recipients = $conversation->participants()
            ->where('users.id', '!=', $sender->id)
            ->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new NewMessageFromAdminNotification($message));
        }
    }
}
