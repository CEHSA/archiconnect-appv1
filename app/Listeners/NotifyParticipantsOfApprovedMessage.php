<?php

namespace App\Listeners;

use App\Events\MessageApprovedByAdmin;
use App\Models\User;
use App\Notifications\NewApprovedMessageInConversationNotification;
use App\Notifications\YourMessageWasApprovedNotification; // Added
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class NotifyParticipantsOfApprovedMessage implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }

    public function handle(MessageApprovedByAdmin $event): void
    {
        $approvedMessage = $event->message;
        $conversation = $approvedMessage->conversation;

        if (!$conversation) {
            Log::error("NotifyParticipantsOfApprovedMessage: Message ID {$approvedMessage->id} has no associated conversation.");
            return;
        }

        $originalSender = $approvedMessage->user; 
        $approvingAdmin = $event->admin; 

        // Notify the original sender that their message was approved
        if ($originalSender instanceof User && $approvingAdmin instanceof User) {
            // Ensure sender is not the approving admin to avoid self-notification if an admin messages and another approves.
            // This check might be redundant if admin messages are auto-approved and don't go through this flow.
            if ($originalSender->id !== $approvingAdmin->id) {
                 $originalSender->notify(new YourMessageWasApprovedNotification($approvedMessage, $approvingAdmin));
            }
        }

        // Notify other participants in the conversation (excluding original sender and approving admin)
        $recipients = $conversation->participants()
            ->where('users.id', '!=', $originalSender->id)
            ->where('users.id', '!=', $approvingAdmin->id) 
            ->get();
        
        if ($recipients->isNotEmpty()) {
            Notification::send($recipients, new NewApprovedMessageInConversationNotification($approvedMessage));
            
            // Logging can be removed or made conditional based on environment
            // foreach($recipients as $recipient) {
            //      Log::info("New approved message notification sent to participant: {$recipient->email} for message ID {$approvedMessage->id}.");
            // }
        } else {
            // Logging can be removed or made conditional
            // Log::info("No other participants to notify for approved message ID: {$approvedMessage->id}. Original sender: {$originalSender->email}, Approving Admin: {$approvingAdmin->email}.");
        }
    }
}
