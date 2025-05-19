<?php

namespace App\Events;

use App\Models\WorkSubmission;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkSubmissionReviewedByAdmin
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $workSubmission;

    /**
     * Create a new event instance.
     */
    public function __construct(WorkSubmission $workSubmission)
    {
        $this->workSubmission = $workSubmission;
    }
}
