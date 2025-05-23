<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewApprovedMessageInConversationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Message $approvedMessage;
    public User $originalSender;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $approvedMessage)
    {
        $this->approvedMessage = $approvedMessage;
        $this->originalSender = $approvedMessage->user; // User who originally sent the message
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Do not send to the original sender of the message
        if ($notifiable instanceof User && $notifiable->id === $this->originalSender->id) {
            return ['database']; // Or return [] if no notification needed for original sender via this specific class
        }
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $conversation = $this->approvedMessage->conversation;
        $subject = "New message approved in conversation: " . ($conversation->subject ?? 'Job Discussion');
        $greeting = "Hello {$notifiable->name},";
        $line = "A new message from {$this->originalSender->name} has been approved by an admin in the conversation regarding: \"{$conversation->subject}\".";
        
        $actionUrl = route('login'); // Default fallback
        if ($notifiable instanceof User) {
            // Determine link based on recipient's role
            if ($notifiable->role === User::ROLE_CLIENT) {
                $actionUrl = route('client.messages.show', $conversation->id);
            } elseif ($notifiable->role === User::ROLE_FREELANCER) {
                $actionUrl = route('freelancer.messages.show', $conversation->id);
            } elseif ($notifiable->role === User::ROLE_ADMIN) {
                // If an admin (not the approver, and not the original sender) is also a participant being notified
                if ($notifiable->id !== $this->approvedMessage->reviewed_by_admin_id && $notifiable->id !== $this->originalSender->id) {
                     $actionUrl = route('admin.messages.showConversation', $conversation->id);
                }
            }
        }

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line)
                    ->line("Message: \"" . Str::limit($this->approvedMessage->body, 100) . "\"")
                    ->action('View Conversation', $actionUrl)
                    ->line('Thank you for using Architex Axis.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $conversation = $this->approvedMessage->conversation;
        $actionUrl = route('login'); // Default fallback
         if ($notifiable instanceof User) {
            if ($notifiable->role === User::ROLE_CLIENT) {
                $actionUrl = route('client.messages.show', $conversation->id);
            } elseif ($notifiable->role === User::ROLE_FREELANCER) {
                $actionUrl = route('freelancer.messages.show', $conversation->id);
            } elseif ($notifiable->role === User::ROLE_ADMIN) {
                 if ($notifiable->id !== $this->approvedMessage->reviewed_by_admin_id && $notifiable->id !== $this->originalSender->id) {
                    $actionUrl = route('admin.messages.showConversation', $conversation->id);
                }
            }
        }

        return [
            'conversation_id' => $conversation->id,
            'message_id' => $this->approvedMessage->id,
            'original_sender_id' => $this->originalSender->id,
            'original_sender_name' => $this->originalSender->name,
            'message_body_snippet' => Str::limit($this->approvedMessage->body, 100),
            'message' => "A message from {$this->originalSender->name} was approved: \"" . Str::limit($this->approvedMessage->body, 50) . "\"",
            'link' => $actionUrl,
            'conversation_subject' => $conversation->subject,
        ];
    }
}
