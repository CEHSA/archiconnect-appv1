<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class YourMessageWasApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Message $approvedMessage;
    public User $approvingAdmin;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $approvedMessage, User $approvingAdmin)
    {
        $this->approvedMessage = $approvedMessage;
        $this->approvingAdmin = $approvingAdmin;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // This notification is specifically for the original sender
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $conversation = $this->approvedMessage->conversation;
        $subject = "Your message in conversation: \"{$conversation->subject}\" was approved";
        $greeting = "Hello {$notifiable->name},"; // $notifiable is the original sender
        $line = "Your message in the conversation regarding \"{$conversation->subject}\" has been approved by {$this->approvingAdmin->name} (Admin) and is now visible to other participants.";
        
        $actionUrl = route('login'); // Default fallback
        if ($notifiable instanceof User) {
            // Link to the conversation based on user role
            if ($notifiable->role === User::ROLE_FREELANCER) {
                $actionUrl = route('freelancer.messages.show', $conversation->id);
            } elseif ($notifiable->role === User::ROLE_CLIENT) { 
                // This case is unlikely if client messages are auto-approved, but included for completeness
                $actionUrl = route('client.messages.show', $conversation->id);
            }
            // No specific link for admin as sender, as their messages are auto-approved
        }

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line)
                    ->line("Your message: \"" . Str::limit($this->approvedMessage->body, 100) . "\"")
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
             if ($notifiable->role === User::ROLE_FREELANCER) {
                $actionUrl = route('freelancer.messages.show', $conversation->id);
            } elseif ($notifiable->role === User::ROLE_CLIENT) {
                $actionUrl = route('client.messages.show', $conversation->id);
            }
        }

        return [
            'conversation_id' => $conversation->id,
            'message_id' => $this->approvedMessage->id,
            'approving_admin_id' => $this->approvingAdmin->id,
            'approving_admin_name' => $this->approvingAdmin->name,
            'message_body_snippet' => Str::limit($this->approvedMessage->body, 100),
            'message' => "Your message regarding \"{$conversation->subject}\" was approved by {$this->approvingAdmin->name}.",
            'link' => $actionUrl,
            'conversation_subject' => $conversation->subject,
        ];
    }
}
