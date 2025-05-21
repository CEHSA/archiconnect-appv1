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
    public string $actionType;
    public string $description;
    public Job $model; // For LogAdminActivity listener
    public ?\App\Models\Admin $adminUser; // Admin user who performed the action

    /**
     * Create a new event instance.
     */
    public function __construct(Job $job, ?\App\Models\Admin $adminUser = null)
    {
        $this->job = $job;
        $this->model = $job; // Assign job to model property
        $this->adminUser = $adminUser ?? Auth::guard('admin')->user(); // Fallback, but ideally passed
        $this->actionType = 'job_created';

        $adminName = $this->adminUser ? $this->adminUser->name : 'An admin';
        $this->description = "{$adminName} created a new job: {$job->title} (ID: {$job->id})";
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
