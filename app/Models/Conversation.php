<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Admin; // Added import for Admin model

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'participant1_id',
        'participant1_type',
        'participant2_id',
        'participant2_type',
        'job_id',
        'job_assignment_id',
        'subject',
        'status',
        'last_message_at',
    ];

    /**
     * Get the first participant of the conversation.
     */
    public function participant1(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the second participant of the conversation.
     */
    public function participant2(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * The job this conversation is associated with (optional).
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * The messages in this conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * The users participating in this conversation.
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user')->withTimestamps()->withPivot('last_read_at');
    }

    /**
     * Get the latest message in the conversation.
     */
    public function getLatestMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Get conversations for a specific user or admin.
     */
    public function scopeForUser($query, User|Admin $user) // Changed User to User|Admin
    {
        return $query->where(function ($q) use ($user) {
                        $q->where('participant1_id', $user->id)
                          ->where('participant1_type', get_class($user));
                    })->orWhere(function ($q) use ($user) {
                        $q->where('participant2_id', $user->id)
                          ->where('participant2_type', get_class($user));
                    });
    }

    /**
     * Get unread messages count for a user or admin.
     * Assumes messages.user_id can correspond to an ID from either users or admins table,
     * and that $user->id provides the correct comparable ID.
     */
    public function unreadCount(User|Admin $user): int // Changed User to User|Admin
    {
        return $this->messages()
                    ->whereNull('read_at')
                    ->where('user_id', '!=', $user->id) // Assumes $user->id is the correct field for comparison against messages.user_id
                    ->count();
    }

    /**
     * Check if a user or admin is a participant in this conversation.
     */
    public function isParticipant(User|Admin $user): bool // Changed User to User|Admin
    {
        return ($this->participant1_id === $user->id && $this->participant1_type === get_class($user)) ||
               ($this->participant2_id === $user->id && $this->participant2_type === get_class($user));
    }
}
