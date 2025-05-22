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
    public function scopeForUser($query, User $user) // This scope is specific to User model
    {
        return $query->where(function ($q) use ($user) {
                        $q->where('participant1_id', $user->id)
                          ->where('participant1_type', 'user');
                    })->orWhere(function ($q) use ($user) {
                        $q->where('participant2_id', $user->id)
                          ->where('participant2_type', 'user');
                    });
    }

    /**
     * Get unread messages count for a participant (User or Admin).
     * @param \App\Models\User|\App\Models\Admin $participant
     */
    public function unreadCount($participant): int // Removed User type hint
    {
        // Ensure participant is a valid model instance with an id property
        if (!is_object($participant) || !property_exists($participant, 'id')) {
            return 0;
        }

        return $this->messages()
                    ->whereNull('read_at')
                    // Check that the message's user_id is not the participant's id.
                    // This assumes messages.user_id can hold IDs from User or Admin model
                    // and that these IDs are distinct or Message model's user() relationship is polymorphic.
                    ->where('user_id', '!=', $participant->id)
                    ->count();
    }

    /**
     * Check if a user is a participant in the conversation.
     * This method should ideally accept a more generic Authenticatable or a shared interface.
     */
    public function isParticipant($participant): bool
    {
        if (!is_object($participant) || !property_exists($participant, 'id') || !method_exists($participant, 'getMorphClass')) {
            return false;
        }
        return ($this->participant1_id === $participant->id && $this->participant1_type === $participant->getMorphClass()) ||
               ($this->participant2_id === $participant->id && $this->participant2_type === $participant->getMorphClass());
    }

    /**
     * Get the other participant in the conversation.
     * This method should also accept a more generic type.
     */
    public function getOtherParticipant($currentUser): ?Model
    {
        if (!is_object($currentUser) || !property_exists($currentUser, 'id') || !method_exists($currentUser, 'getMorphClass')) {
            return null;
        }

        if ($this->participant1_id === $currentUser->id && $this->participant1_type === $currentUser->getMorphClass()) {
            return $this->participant2;
        }
        if ($this->participant2_id === $currentUser->id && $this->participant2_type === $currentUser->getMorphClass()) {
            return $this->participant1;
        }
        return null;
    }

    /**
     * Helper to determine if a user is an admin participant.
     * This is a basic example; you might have a more robust way to check admin roles.
     */
    protected function isAdminParticipant($participant): bool
    {
        if (!is_object($participant) || !property_exists($participant, 'id') || !method_exists($participant, 'getMorphClass')) {
            return false;
        }
        return ($this->participant1_id === $participant->id && $this->participant1_type === Admin::class) || // Assuming Admin::class is the morph key
               ($this->participant2_id === $participant->id && $this->participant2_type === Admin::class);
    }
}
