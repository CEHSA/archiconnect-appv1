<?php

namespace App\Listeners;

use App\Events\MessageApprovedByAdmin;
use App\Models\Message;
use App\Models\User;
use App\Models\Admin;
use App\Mail\ClientApprovedMessageNotification; // This mailer might need to be generalized or duplicated
use App\Notifications\MessageApprovedByAdminDbNotification; // This notification might need to be generalized or duplicated
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
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

        $sender = $approvedMessage->user; // User who sent the message that was approved

        // Notify the sender that their message was approved
        if ($sender instanceof User) { // Assuming sender is always a User model instance
            // TODO: Create a specific notification for the sender
            // For now, let's log this. A 'YourMessageApprovedNotification' could be created.
            Log::info("Message ID {$approvedMessage->id} by sender {$sender->email} was approved. (Sender notification pending implementation)");
            // $sender->notify(new YourMessageApprovedNotification($approvedMessage));
        }

        // Notify other participants in the conversation
        $participantsToNotify = collect();

        if ($conversation->participant1_type === User::class && $conversation->participant1_id !== $sender->id) {
            $user = User::find($conversation->participant1_id);
            if ($user) $participantsToNotify->push($user);
        } elseif ($conversation->participant1_type === Admin::class) {
            // Optionally notify admin participant if needed, or skip
        }


        if ($conversation->participant2_type === User::class && $conversation->participant2_id !== $sender->id) {
            $user = User::find($conversation->participant2_id);
            if ($user) $participantsToNotify->push($user);
        } elseif ($conversation->participant2_type === Admin::class) {
            // Optionally notify admin participant if needed, or skip
        }
        
        // If conversation is tied to a job, the job owner (client) might also be a participant or need notification
        // The current logic in NotifyClientOfApprovedMessage tries to find a 'client'.
        // We need to ensure we don't double-notify if they are already part of participant1/2.
        if ($conversation->job && $conversation->job->user_id) {
            $jobOwner = User::find($conversation->job->user_id);
            if ($jobOwner && $jobOwner->id !== $sender->id && !$participantsToNotify->contains('id', $jobOwner->id)) {
                if ($jobOwner->hasRole(User::ROLE_CLIENT)) { // Ensure it's a client
                     $participantsToNotify->push($jobOwner);
                }
            }
        }
        
        $participantsToNotify = $participantsToNotify->unique('id');

        if ($participantsToNotify->isNotEmpty()) {
            foreach ($participantsToNotify as $participant) {
                if ($participant instanceof User && $participant->email) {
                    // Using existing Mailer and DB Notification for now, these might need to be generalized
                    // if the content needs to be different for "new message in conversation" vs "your message approved".
                    Mail::to($participant->email)
                        ->send(new ClientApprovedMessageNotification($approvedMessage)); // Consider renaming/generalizing this Mailable

                    Notification::send($participant, new MessageApprovedByAdminDbNotification($approvedMessage)); // Consider renaming/generalizing
                    
                    Log::info("Approved message notification sent to participant: {$participant->email} for message ID {$approvedMessage->id}.");
                }
            }
        } else {
            Log::info("No other participants to notify for approved message ID: {$approvedMessage->id}. Sender was {$sender->email}.");
        }
    }
}
