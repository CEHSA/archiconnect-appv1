<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_posting_id',
        'freelancer_id',
        'job_id',
        'cover_letter',
        'proposed_rate',
        'estimated_timeline',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'proposed_rate' => 'decimal:2',
    ];

    /**
     * Get the job posting associated with the application.
     */
    public function jobPosting(): BelongsTo
    {
        return $this->belongsTo(JobPosting::class);
    }

    /**
     * Get the freelancer (user) who submitted the application.
     */
    public function freelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    /**
     * Get the job associated with the application.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }
}
