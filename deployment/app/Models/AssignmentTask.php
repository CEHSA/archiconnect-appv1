<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssignmentTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_assignment_id',
        'title',
        'description',
        'status',
        'due_date',
        'order',
    ];

    /**
     * Get the job assignment that this task belongs to.
     */
    public function jobAssignment(): BelongsTo
    {
        return $this->belongsTo(JobAssignment::class);
    }

    /**
     * Get the time logs for the assignment task.
     */
    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class);
    }
}
