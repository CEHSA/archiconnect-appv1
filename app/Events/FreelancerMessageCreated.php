<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use App\Models\Message; // Import the Message model
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FreelancerMessageCreated implements ShouldBroadcast // Implement ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message; // Public property to hold the message

    /**
     * Create a new event instance.
     */
    public function __construct(Message $message) // Accept Message instance
    {
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // Broadcast to a private channel for admins
            new PrivateChannel('admin.messages.pending'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'user_id' => $this->message->user_id,
            'content' => $this->message->content,
            'created_at' => $this->message->created_at, // Use the Carbon instance directly
            'user_name' => $this->message->user->name, // Include sender's name
            'conversation_title' => $this->message->conversation->title ?? 'N/A', // Include conversation title
        ];
    }
}
