<?php

namespace App\Events;

use App\Models\WorkSubmission;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkSubmissionSubmittedToClient
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public WorkSubmission $submission;

    /**
     * Create a new event instance.
     */
    public function __construct(WorkSubmission $submission)
    {
        $this->submission = $submission;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // This event is not intended for real-time broadcasting initially,
        // but could be a private channel to the client or admin if needed.
        // Example: return [new PrivateChannel('channel-name')];
        return [];
    }
}
