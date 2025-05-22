<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class TimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_task_id',
        'freelancer_id',
        'start_time',
        'end_time',
        'duration_seconds',
        'freelancer_comments',
        'proof_of_work_path',
        'proof_of_work_filename',
        'status',
        'admin_comments',
        'reviewed_by_admin_id',
        'reviewed_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public const STATUS_RUNNING = 'running';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DECLINED = 'declined';

    public function assignmentTask(): BelongsTo
    {
        return $this->belongsTo(AssignmentTask::class);
    }

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function reviewedByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'reviewed_by_admin_id');
    }

    public function getDurationForHumansAttribute(): string
    {
        if (!$this->duration_seconds) {
            return 'N/A';
        }
        return Carbon::now()->subSeconds($this->duration_seconds)->diffForHumans(null, true);
    }
}
