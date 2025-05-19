<?php

namespace App\Events;

use App\Models\Dispute;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DisputeUpdatedByAdmin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Dispute $dispute;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Dispute $dispute
     * @return void
     */
    public function __construct(Dispute $dispute)
    {
        $this->dispute = $dispute;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // This event is primarily for backend notifications, not typically broadcast to frontend channels directly.
        // If real-time updates for involved users are needed, private channels could be defined here.
        // e.g., new PrivateChannel('disputes.' . $this->dispute->id)
        return [];
    }
}
