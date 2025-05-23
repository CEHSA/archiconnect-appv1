<?php

namespace App\Notifications;

use App\Models\Message;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class NewMessageFromAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Message $message;
    public User $sender; // The admin who sent the message

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
        $this->sender = $message->user; // Assuming the user relationship on Message is loaded
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Do not send email to self if the notifiable is the sender (e.g. another admin in same convo)
        if ($notifiable instanceof User && $notifiable->id === $this->sender->id) {
            return ['database'];
        }
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $conversation = $this->message->conversation;
        $subject = "New message from Admin in conversation: " . ($conversation->subject ?? 'Job Discussion');
        $greeting = "Hello {$notifiable->name},";
        $line = "You have a new message from {$this->sender->name} (Admin) in the conversation regarding: \"{$conversation->subject}\".";
        
        $actionUrl = route('login'); // Default fallback
        if ($notifiable instanceof User) {
            if ($notifiable->role === User::ROLE_FREELANCER) {
                $actionUrl = route('freelancer.messages.show', $conversation->id);
            } elseif ($notifiable->role === User::ROLE_CLIENT) {
                $actionUrl = route('client.messages.show', $conversation->id);
            } elseif ($notifiable->role === User::ROLE_ADMIN && $notifiable->id !== $this->sender->id) {
                 // Link for other admins in the same conversation
                $actionUrl = route('admin.messages.showConversation', $conversation->id);
            }
        }

        return (new MailMessage)
                    ->subject($subject)
                    ->greeting($greeting)
                    ->line($line)
                    ->line("Message: \"" . Str::limit($this->message->body, 100) . "\"")
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
        $conversation = $this->message->conversation;
        $actionUrl = route('login'); // Default fallback
        if ($notifiable instanceof User) {
            if ($notifiable->role === User::ROLE_FREELANCER) {
                $actionUrl = route('freelancer.messages.show', $conversation->id);
            } elseif ($notifiable->role === User::ROLE_CLIENT) {
                $actionUrl = route('client.messages.show', $conversation->id);
            } elseif ($notifiable->role === User::ROLE_ADMIN && $notifiable->id !== $this->sender->id) {
                $actionUrl = route('admin.messages.showConversation', $conversation->id);
            }
        }

        return [
            'conversation_id' => $conversation->id,
            'message_id' => $this->message->id,
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name . " (Admin)",
            'message_body_snippet' => Str::limit($this->message->body, 100),
            'message' => "New message from {$this->sender->name} (Admin): \"" . Str::limit($this->message->body, 50) . "\"",
            'link' => $actionUrl,
            'conversation_subject' => $conversation->subject,
        ];
    }
}
