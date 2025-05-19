<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class JobComment extends Model
{
    protected $fillable = [
        'job_id',
        'user_id',
        'comment_text',
        'status',
        'parent_comment_id',
        'discussed_at',
        'resolved_at',
    ];

    protected $casts = [
        'discussed_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    // Status constants
    const STATUS_NEW = 'new';
    const STATUS_DISCUSSED = 'discussed';
    const STATUS_PENDING_FREELANCER = 'pending_freelancer';
    const STATUS_RESOLVED = 'resolved';

    /**
     * Get the job that the comment belongs to.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the user who made the comment.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent comment if this is a reply.
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(JobComment::class, 'parent_comment_id');
    }

    /**
     * Get the replies to this comment.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(JobComment::class, 'parent_comment_id');
    }

    /**
     * Format created_at timestamp for display.
     */
    public function getFormattedCreatedAtAttribute(): string
    {
        return Carbon::parse($this->created_at)->format('M d, Y H:i A');
    }

    /**
     * Mark the comment as discussed by the freelancer.
     */
    public function markAsDiscussed(): bool
    {
        return $this->update([
            'status' => self::STATUS_DISCUSSED,
            'discussed_at' => now(),
        ]);
    }

    /**
     * Mark the comment as pending freelancer action.
     */
    public function markAsPendingFreelancer(): bool
    {
        return $this->update([
            'status' => self::STATUS_PENDING_FREELANCER,
            'discussed_at' => null, // Reset discussed timestamp if it was set
            'resolved_at' => null, // Reset resolved timestamp if it was set
        ]);
    }

    /**
     * Mark the comment as resolved by the admin.
     */
    public function markAsResolved(): bool
    {
        return $this->update([
            'status' => self::STATUS_RESOLVED,
            'resolved_at' => now(),
        ]);
    }

    /**
     * Scope a query to only include comments with a specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get comments that need attention (new or pending_freelancer).
     */
    public function scopeNeedsAttention($query)
    {
        return $query->whereIn('status', [self::STATUS_NEW, self::STATUS_PENDING_FREELANCER]);
    }

    /**
     * Check if the comment needs attention.
     */
    public function needsAttention(): bool
    {
        return in_array($this->status, [self::STATUS_NEW, self::STATUS_PENDING_FREELANCER]);
    }

    /**
     * Check if the comment is resolved.
     */
    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    /**
     * Check if the comment is being discussed.
     */
    public function isDiscussed(): bool
    {
        return $this->status === self::STATUS_DISCUSSED;
    }

    /**
     * Check if the comment is pending freelancer action.
     */
    public function isPendingFreelancer(): bool
    {
        return $this->status === self::STATUS_PENDING_FREELANCER;
    }

    /**
     * Get the formatted discussed_at date.
     */
    public function getFormattedDiscussedAtAttribute(): ?string
    {
        return $this->discussed_at ? Carbon::parse($this->discussed_at)->format('M d, Y H:i A') : null;
    }

    /**
     * Get the formatted resolved_at date.
     */
    public function getFormattedResolvedAtAttribute(): ?string
    {
        return $this->resolved_at ? Carbon::parse($this->resolved_at)->format('M d, Y H:i A') : null;
    }
}
