<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FreelancerMessageCreatedDbNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Message $message;

    /**
     * Create a new notification instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $conversation = $this->message->conversation;
        $jobTitle = $conversation->job->title ?? 'N/A';
        $senderName = $this->message->sender->name ?? 'A user';

        return [
            'title' => 'New Message from Freelancer',
            'message' => "A new message from {$senderName} regarding job '{$jobTitle}' requires your review.",
            'message_id' => $this->message->id,
            'conversation_id' => $conversation->id,
            'job_id' => $conversation->job_id,
            'url' => route('admin.messages.show', $this->message->id) // Link to admin message review
        ];
    }
}
