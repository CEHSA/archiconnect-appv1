<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\JobAssignment; // Add this line

class JobCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The job assignment instance.
     *
     * @var \App\Models\JobAssignment
     */
    public $jobAssignment;

    /**
     * Create a new event instance.
     */
    public function __construct(JobAssignment $jobAssignment)
    {
        $this->jobAssignment = $jobAssignment;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('job-completed.' . $this->jobAssignment->id),
        ];
    }
}
