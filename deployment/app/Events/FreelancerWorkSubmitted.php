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

class FreelancerWorkSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public WorkSubmission $workSubmission;

    /**
     * Create a new event instance.
     */
    public function __construct(WorkSubmission $workSubmission)
    {
        $this->workSubmission = $workSubmission;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // This event is not intended for broadcasting to clients via WebSockets,
        // but rather for server-side listeners (like sending emails).
        // If broadcasting were needed, it might be on a private channel for admins.
        return [
            // new PrivateChannel('admin-notifications'), 
        ];
    }
}
