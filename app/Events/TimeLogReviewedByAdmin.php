<?php

namespace App\Events;

use App\Models\TimeLog;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TimeLogReviewedByAdmin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public TimeLog $timeLog;

    /**
     * Create a new event instance.
     */
    public function __construct(TimeLog $timeLog)
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
        // Broadcast to the freelancer who submitted the time log
        return [
            new PrivateChannel('users.' . $this->timeLog->freelancer_id),
        ];
    }
}
