<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
// use App\Models\Admin; // Removed unused import

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        // 'participant1_id', // Removed
        // 'participant1_type', // Removed
        // 'participant2_id', // Removed
        // 'participant2_type', // Removed
        'job_id',
        'job_assignment_id',
        'created_by_user_id', // Added: who initiated the conversation
        'subject',
        'status', // e.g., open, closed, archived
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    // Removed participant1() and participant2() MorphTo relationships

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
    public function scopeForUser($query, User $user) // Type hint to User, assuming Admins are also Users with a role
    {
        // Use the participants relationship (many-to-many)
        return $query->whereHas('participants', function ($q) use ($user) {
            $q->where('users.id', $user->id); // Ensure you are querying the users table correctly
        });
    }

    /**
     * Get unread messages count for a user.
     */
    public function unreadCount(User $user): int
    {
        // Get the last_read_at timestamp for the user in this conversation from the pivot table
        $lastReadAt = $this->participants()->where('users.id', $user->id)->first()?->pivot->last_read_at;

        return $this->messages()
                    ->where('user_id', '!=', $user->id) // Messages not sent by the current user
                    ->when($lastReadAt, function ($query) use ($lastReadAt) {
                        return $query->where('created_at', '>', $lastReadAt);
                    }, function ($query) {
                        // If never read, all messages (not by user) are unread
                        return $query;
                    })
                    ->count();
    }
    
    /**
     * Mark messages as read for a user up to a certain point (e.g., now).
     */
    public function markAsReadForUser(User $user, ?\Carbon\Carbon $timestamp = null): void
    {
        $timestamp = $timestamp ?? now();
        $this->participants()->updateExistingPivot($user->id, ['last_read_at' => $timestamp]);
    }


    /**
     * Check if a user is a participant in this conversation.
     */
    public function isParticipant(User $user): bool
    {
        return $this->participants()->where('users.id', $user->id)->exists();
    }

    /**
     * Get the user who created the conversation.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
