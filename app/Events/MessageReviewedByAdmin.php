<?php

namespace App\Events;

use App\Models\Admin;
use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReviewedByAdmin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public Admin $adminUser;
    public string $actionTaken; // 'approved' or 'rejected'
    public string $actionType;
    public string $description;
    public Message $model;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Message $message
     * @param \App\Models\Admin $adminUser
     * @param string $actionTaken ('approved' or 'rejected')
     */
    public function __construct(Message $message, Admin $adminUser, string $actionTaken)
    {
        $this->message = $message;
        $this->adminUser = $adminUser;
        $this->actionTaken = $actionTaken;
        
        $this->model = $this->message;
        $this->actionType = 'message_' . $this->actionTaken; // e.g., 'message_approved'
        $this->description = "Admin {$this->adminUser->name} (ID: {$this->adminUser->id}) {$this->actionTaken} message ID {$this->message->id}.";
    }
}
