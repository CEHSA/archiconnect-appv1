<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Job;
use App\Models\User; // Assuming User model is used for admin

class JobCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The job instance.
     *
     * @var \App\Models\Job
     */
    public $job;

    /**
     * The admin user who completed the job.
     *
     * @var \App\Models\User|null
     */
    public $admin;
    public $notes;
    public $completionStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Job $job, ?User $admin = null, string $notes = '', string $completionStatus = 'completed')
    {
        $this->job = $job;
        $this->admin = $admin;
        $this->notes = $notes;
        $this->completionStatus = $completionStatus;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('job-completed.' . $this->job->id),
            new PrivateChannel('admin-notifications'),
            new PrivateChannel('client-notifications.' . $this->job->client_id),
            new PrivateChannel('freelancer-notifications.' . $this->job->freelancer_id),
        ];
    }
}
