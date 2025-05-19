<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany; // Corrected import

class Dispute extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_assignment_id',
        'reporter_id',
        'reported_id',
        'reason',
        'evidence_path',
        'status',
        'admin_remarks',
        'client_remarks',
    ];

    /**
     * Get the job assignment associated with the dispute.
     */
    public function jobAssignment(): BelongsTo
    {
        return $this->belongsTo(JobAssignment::class);
    }

    /**
     * Get the user who reported the dispute.
     */
    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    /**
     * Get the user who is the subject of the dispute.
     */
    public function reportedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_id');
    }

    /**
     * Get the updates for the dispute.
     */
    public function updates(): HasMany // The type hint itself is correct if the import is correct
    {
        return $this->hasMany(DisputeUpdate::class)->orderBy('created_at', 'asc');
    }
}
