<?php

namespace App\Events;

use App\Models\Job;
use App\Models\JobAssignment;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JobAcceptanceRequested
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Job $job;
    public JobAssignment $jobAssignment;
    public User $freelancer;

    /**
     * Create a new event instance.
     *
     * @param Job $job
     * @param JobAssignment $jobAssignment
     * @param User $freelancer
     */
    public function __construct(Job $job, JobAssignment $jobAssignment, User $freelancer)
    {
        $this->job = $job;
        $this->jobAssignment = $jobAssignment;
        $this->freelancer = $freelancer;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        // This event is not intended for broadcasting to the frontend directly,
        // but rather for server-side listeners (e.g., to send notifications).
        // If broadcasting were needed, it might be a private channel to admins.
        // For now, returning an empty array or a generic private channel.
        return [
            // new PrivateChannel('admin-notifications'), 
        ];
    }
}
