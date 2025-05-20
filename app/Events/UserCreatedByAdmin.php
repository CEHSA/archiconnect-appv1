<?php

namespace App\Events;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreatedByAdmin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $model; // The User model that was created
    public Admin $admin; // The Admin who performed the action
    public string $actionType;
    public string $description;

    /**
     * Create a new event instance.
     *
     * @param User $user The user that was created.
     * @param Admin $admin The admin who performed the action.
     */
    public function __construct(User $user, Admin $admin)
    {
        $this->model = $user;
        $this->admin = $admin; // Storing the admin, though LogAdminActivity currently re-fetches it.
        $this->actionType = 'user_created';
        $this->description = "Admin {$admin->name} (ID: {$admin->id}) created user {$user->name} (ID: {$user->id}).";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'), // Placeholder, not broadcasting for now
        ];
    }
}
