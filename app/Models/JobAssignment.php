<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'client_id', // Added client_id
        'freelancer_id',
        'assigned_by_admin_id',
        'status',
        'freelancer_remarks',
        'admin_remarks',
    ];

    /**
     * Get the job that this assignment belongs to.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the client (user) for this job assignment.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the freelancer (user) that this assignment is for.
     */
    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    /**
     * Get the admin (user) who made this assignment.
     */
    public function assignedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_by_admin_id');
    }

    /**
     * Get the time logs for this job assignment.
     */
    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class);
    }

    /**
     * Get the work submissions for this job assignment.
     */
    public function workSubmissions(): HasMany
    {
        return $this->hasMany(WorkSubmission::class);
    }

    /**
     * Get the task progress updates for this job assignment.
     */
    public function taskProgress(): HasMany
    {
        return $this->hasMany(TaskProgress::class);
    }

    /**
     * Get the disputes related to this job assignment.
     */
    public function disputes(): HasMany
    {
        return $this->hasMany(Dispute::class);
    }

    /**
     * Get the tasks for this job assignment.
     */
    public function tasks(): HasMany
    {
        return $this->hasMany(AssignmentTask::class);
    }
}
