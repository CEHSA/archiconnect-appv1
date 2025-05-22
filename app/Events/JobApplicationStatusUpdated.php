<?php

namespace App\Events;

use App\Models\JobApplication;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobApplicationStatusUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public JobApplication $application;
    public string $oldStatus; // Optional: to include the previous status

    /**
     * Create a new event instance.
     */
    public function __construct(JobApplication $application, ?string $oldStatus = null)
    {
        $this->application = $application;
        $this->oldStatus = $oldStatus ?? $application->getOriginal('status'); // Get original if not passed
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // Example: Private channel for the freelancer who applied
        // return [
        //     new PrivateChannel('user.' . $this->application->freelancer_id),
        // ];
        return []; // No broadcasting by default for now
    }
}
