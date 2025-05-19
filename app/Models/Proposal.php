<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Proposal extends Model
{
    use HasFactory;
    protected $fillable = [
        'job_id',
        'user_id',
        'cover_letter',
        'proposed_budget',
        'status',
        'admin_remarks'
    ];

    /**
     * Get the job that owns the proposal.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the user (freelancer) who made the proposal.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
