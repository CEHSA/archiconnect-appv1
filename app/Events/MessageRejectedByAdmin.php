<?php

namespace App\Events;

use App\Models\Admin;
use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageRejectedByAdmin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Message $message;
    public Admin $adminUser;
    public string $remarks;

    /**
     * Create a new event instance.
     *
     * @param \App\Models\Message $message
     * @param \App\Models\Admin $adminUser
     * @param string|null $remarks
     */
    public function __construct(Message $message, Admin $adminUser, ?string $remarks)
    {
        $this->message = $message;
        $this->adminUser = $adminUser;
        $this->remarks = $remarks ?? '';
    }
}
