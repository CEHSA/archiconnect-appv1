<?php

namespace App\Events;

use App\Models\FreelancerTimeLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class FreelancerTimeLogStarted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public FreelancerTimeLog $timeLog;

    /**
     * Create a new event instance.
     */
    public function __construct(FreelancerTimeLog $timeLog)
    {
        $this->timeLog = $timeLog;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'), // Replace with actual channel if broadcasting
        ];
    }
}
