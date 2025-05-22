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

class JobCommentStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $jobComment;
    public $oldStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(JobComment $jobComment, string $oldStatus)
    {
        $this->jobComment = $jobComment;
        $this->oldStatus = $oldStatus;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // Broadcast to job-specific channel and admin channel
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
            'old_status' => $this->oldStatus,
            'new_status' => $this->jobComment->status,
            'discussed_at' => $this->jobComment->formatted_discussed_at,
            'resolved_at' => $this->jobComment->formatted_resolved_at,
            'updated_at' => now()->format('M d, Y H:i A'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'job-comment.status-updated';
    }
}
