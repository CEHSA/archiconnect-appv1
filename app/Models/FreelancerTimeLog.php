<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FreelancerTimeLog extends Model
{
    use HasFactory;

    protected $table = 'freelancer_time_logs';

    protected $fillable = [
        'freelancer_id',
        'job_assignment_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'notes',
        'status',
        'is_offline_recorded', // Added for offline tracking
        'offline_id',          // Added for offline tracking unique ID
        'reviewed_by_admin_id',
        'reviewed_at',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function jobAssignment(): BelongsTo
    {
        return $this->belongsTo(JobAssignment::class, 'job_assignment_id');
    }

    public const STATUS_RUNNING = 'running';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_DECLINED = 'declined';

    public function reviewedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_admin_id');
    }
}
