<?php

namespace App\Listeners;

use App\Events\MessageApprovedByAdmin;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Mail\ClientApprovedMessageNotification;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Notifications\MessageApprovedByAdminDbNotification; // Add this line
use Illuminate\Support\Facades\Notification; // Add this line
use Illuminate\Support\Facades\Log; // Add this line

class NotifyClientOfApprovedMessage implements ShouldQueue
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
    public function handle(MessageApprovedByAdmin $event): void
    {
        $message = $event->message;
        $conversation = $message->conversation;

        // Determine the client in the conversation
        // The message sender ($message->user) is the freelancer.
        // The client is the other participant in the conversation related to the job.
        $client = null;
        if ($conversation && $conversation->job && $conversation->job->user_id) {
            // Assuming job->user_id is the client's ID
            $clientUser = User::find($conversation->job->user_id);
            if ($clientUser && $clientUser->hasRole(User::ROLE_CLIENT)) {
                $client = $clientUser;
            }
        }

        // Fallback or alternative logic if job->user_id isn't the client or not available
        if (!$client && $conversation) {
            // This logic assumes a two-party conversation (freelancer and client)
            // and the message sender is one of them.
            if ($conversation->participant1_id === $message->user_id) {
                $client = $conversation->participant2;
            } else {
                $client = $conversation->participant1;
            }
        }
        
        if ($client && $client instanceof User && $client->email) {
            // Send Email Notification
            Mail::to($client->email)
                ->send(new ClientApprovedMessageNotification($message));

            // Send Database Notification
            Notification::send($client, new MessageApprovedByAdminDbNotification($message));

            Log::info("Approved message notification (email and DB) sent to client: {$client->email} for message ID {$message->id}.");

        } else {
            Log::error("Could not determine client or client email for approved message notification. Message ID: {$message->id}");
        }
    }
}
