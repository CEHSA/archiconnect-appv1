<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Add this line

class TaskProgress extends Model
{
    protected $fillable = [
        'job_assignment_id',
        'freelancer_id',
        'description',
        'file_path',
        'submitted_at',
    ];

    /**
     * Get the job assignment that the task progress belongs to.
     */
    public function jobAssignment(): BelongsTo
    {
        return $this->belongsTo(JobAssignment::class);
    }

    /**
     * Get the freelancer who submitted the task progress.
     */
    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}
