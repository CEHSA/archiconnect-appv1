<?php

namespace App\Listeners;

use App\Events\ClientMessageSent;
use App\Models\User;
use App\Notifications\NewMessageFromClientNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Notification;

class NotifyParticipantsOfClientMessage implements ShouldQueue
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
    public function handle(ClientMessageSent $event): void
    {
        $message = $event->message;
        $conversation = $message->conversation;
        $sender = $message->user; // This is the client who sent the message

        // Get all participants of the conversation except the sender
        $recipients = $conversation->participants()
            ->where('users.id', '!=', $sender->id)
            ->get();

        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new NewMessageFromClientNotification($message));
        }
    }
}
