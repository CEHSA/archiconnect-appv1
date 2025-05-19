<?php

namespace App\Events;

use App\Models\Job;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AdminJobPosted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Job $job;

    /**
     * Create a new event instance.
     */
    public function __construct(Job $job)
    {
        $this->job = $job;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            // Potentially a private channel for admin overview or public for job boards later
            // For now, this event is primarily for email notifications
        ];
    }
}
