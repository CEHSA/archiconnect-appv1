<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkSubmission extends Model
{
    use HasFactory;

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
}
