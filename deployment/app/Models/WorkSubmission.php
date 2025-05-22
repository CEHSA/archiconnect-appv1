<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // Added

class WorkSubmission extends Model
{
    use HasFactory;

    // Status Constants
    const STATUS_PENDING_SUBMISSION = 'pending_submission';
    const STATUS_SUBMITTED_FOR_ADMIN_REVIEW = 'submitted_for_admin_review';
    const STATUS_ADMIN_REVISION_REQUESTED = 'admin_revision_requested';
    const STATUS_PENDING_CLIENT_REVIEW = 'pending_client_review';
    const STATUS_CLIENT_REVISION_REQUESTED = 'client_revision_requested';
    const STATUS_APPROVED_BY_CLIENT = 'approved_by_client';
    const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'job_assignment_id',
        'freelancer_id',
        'admin_id',
        'title',
        'description',
        'file_path',
        'original_filename',
        'mime_type',
        'size',
        'status',
        'submitted_at',
        'reviewed_at',
        'admin_remarks',
        'client_remarks', // Added
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'size' => 'integer',
    ];

    /**
     * Get the job assignment that this submission belongs to.
     */
    public function jobAssignment(): BelongsTo
    {
        return $this->belongsTo(JobAssignment::class);
    }

    /**
     * Get the freelancer who made this submission.
     */
    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    /**
     * Get the admin who reviewed/handled this submission.
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get the comments for the work submission.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(JobComment::class);
    }
}
