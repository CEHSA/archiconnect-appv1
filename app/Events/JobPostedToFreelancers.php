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
use Illuminate\Support\Collection;

class JobPostedToFreelancers
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Job $job;
    public array $freelancerIds;

    /**
     * Create a new event instance.
     *
     * @param Job $job The job that was posted.
     * @param array $freelancerIds An array of IDs of freelancers to whom the job was posted.
     */
    public function __construct(Job $job, array $freelancerIds)
    {
        $this->job = $job;
        $this->freelancerIds = $freelancerIds;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // This event might not need to be broadcast directly if notifications are handled by listeners.
        // If real-time updates are needed for specific users, define private channels here.
        // Example: return [new PrivateChannel('job-postings')];
        return [];
    }
}
