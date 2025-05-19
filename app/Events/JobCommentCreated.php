<?php

namespace App\Events;

use App\Models\JobComment;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobCommentCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $jobComment;

    /**
     * Create a new event instance.
     */
    public function __construct(JobComment $jobComment)
    {
        $this->jobComment = $jobComment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // Broadcast to both admin and freelancer channels
            new PrivateChannel('job.' . $this->jobComment->job_id . '.comments'),
            new PrivateChannel('admin.comments'),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->jobComment->id,
            'job_id' => $this->jobComment->job_id,
            'user_id' => $this->jobComment->user_id,
            'comment_text' => $this->jobComment->comment_text,
            'status' => $this->jobComment->status,
            'created_at' => $this->jobComment->formatted_created_at,
            'updated_at' => now()->format('M d, Y H:i A'),
        ];
    }
}
