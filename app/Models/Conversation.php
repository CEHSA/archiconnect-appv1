<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Conversation extends Model
{
    protected $fillable = [
        'participant1_id',
        'participant1_type',
        'participant2_id',
        'participant2_type',
        'job_id',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
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
     * Get the job associated with the conversation.
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class);
    }

    /**
     * Get the messages for the conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Get conversations for a specific user.
     */
    public function scopeForUser($query, User $user)
    {
        return $query->where(function ($q) use ($user) {
                        $q->where('participant1_id', $user->id)
                          ->where('participant1_type', 'user'); // Assuming 'user' for clients/freelancers
                    })->orWhere(function ($q) use ($user) {
                        $q->where('participant2_id', $user->id)
                          ->where('participant2_type', 'user');
                    });
    }

    /**
     * Get unread messages count for a user.
     */
    public function unreadCount(User $user): int
    {
        return $this->messages()
                    ->whereNull('read_at')
                    ->where('user_id', '!=', $user->id)
                    ->count();
    }

    /**
     * Check if a user is a participant in the conversation.
     */
    public function isParticipant(User $user): bool
    {
        return ($this->participant1_id === $user->id && $this->participant1_type === 'user') ||
               ($this->participant2_id === $user->id && $this->participant2_type === 'user') ||
               ($this->participant1_id === $user->id && $this->participant1_type === 'admin') ||
               ($this->participant2_id === $user->id && $this->participant2_type === 'admin');
    }

    /**
     * Get the other participant in the conversation.
     */
    public function getOtherParticipant(User $user): ?User
    {
        if ($this->participant1_id === $user->id && $this->participant1_type === 'user') {
            return $this->participant2;
        }
        if ($this->participant2_id === $user->id && $this->participant2_type === 'user') {
            return $this->participant1;
        }
        // Handle cases where one participant might be an admin
        // This logic might need refinement based on how admin participants are stored/typed
        if ($this->participant1_type === 'admin' && $this->participant2_id === $user->id) {
            return $this->participant1; // Assuming admin is participant1
        }
        if ($this->participant2_type === 'admin' && $this->participant1_id === $user->id) {
            return $this->participant2; // Assuming admin is participant2
        }


        return null;
    }

    /**
     * Helper to determine if a user is an admin participant.
     * This is a basic example; you might have a more robust way to check admin roles.
     */
    protected function isAdminParticipant(User $user): bool
    {
        // Assuming 'admin' role is stored in the user model or participant_type
        return ($this->participant1_id === $user->id && $this->participant1_type === 'admin') ||
               ($this->participant2_id === $user->id && $this->participant2_type === 'admin');
    }
}
