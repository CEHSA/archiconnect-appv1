<?php

namespace App\Listeners;

use App\Events\MessageRejectedByAdmin;
use App\Models\User;
use App\Notifications\MessageRejectedDbNotification; // Assuming this will be created
use App\Mail\MessageRejectedMail; // Assuming this will be created
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;

class NotifySenderOfRejectedMessage implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(MessageRejectedByAdmin $event): void
    {
        $rejectedMessage = $event->message;
        $sender = $rejectedMessage->user; // User who sent the message that was rejected

        if ($sender instanceof User && $sender->email) {
            // TODO: Create specific Mailable and DatabaseNotification classes for rejected messages.
            // For now, logging and using placeholders.
            
            // Send Email Notification (Placeholder for actual Mailable)
            // Mail::to($sender->email)
            //     ->send(new MessageRejectedMail($rejectedMessage, $event->remarks));

            // Send Database Notification (Placeholder for actual Notification class)
            // Notification::send($sender, new MessageRejectedDbNotification($rejectedMessage, $event->remarks));
            
            Log::info("Message ID {$rejectedMessage->id} by sender {$sender->email} was rejected. Remarks: '{$event->remarks}'. (Sender notification pending full implementation of Mailable/Notification class)");
        } else {
            Log::error("Could not determine sender or sender email for rejected message notification. Message ID: {$rejectedMessage->id}");
        }
    }
}
