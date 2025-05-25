<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Admin;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_id',
        'job_assignment_id',
        'created_by_user_id',
        'subject',
        'status',
        'last_message_at',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

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
    public function userParticipants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_user', 'conversation_id', 'user_id')
                    ->withTimestamps()
                    ->withPivot('last_read_at');
    }

    /**
     * The admins participating in this conversation.
     */
    public function adminParticipants(): BelongsToMany
    {
        return $this->belongsToMany(Admin::class, 'conversation_user', 'conversation_id', 'user_id')
                    ->withTimestamps()
                    ->withPivot('last_read_at');
    }

    /**
     * Get all participants (both users and admins).
     */
    public function participants()
    {
        return $this->userParticipants->merge($this->adminParticipants);
    }

    /**
     * Get the latest message in the conversation.
     */
    public function getLatestMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    /**
     * Get conversations for a specific participant (user or admin).
     */
    public function scopeForParticipant($query, $participant)
    {
        return $query->whereHas('userParticipants', function ($q) use ($participant) {
            $q->where('conversation_user.user_id', $participant->id);
        })->orWhereHas('adminParticipants', function ($q) use ($participant) {
            $q->where('conversation_user.user_id', $participant->id);
        });
    }

    /**
     * Get unread messages count for a participant (user or admin).
     */
    public function unreadCount($participant): int
    {
        // Get the last_read_at timestamp for the participant from the pivot table
        $relationship = $participant instanceof Admin ? 'adminParticipants' : 'userParticipants';
        $lastReadAt = $this->{$relationship}()
            ->where('conversation_user.user_id', $participant->id)
            ->first()?->pivot->last_read_at;

        return $this->messages()
            ->where('user_id', '!=', $participant->id)
            ->when($lastReadAt, function ($query) use ($lastReadAt) {
                return $query->where('created_at', '>', $lastReadAt);
            })
            ->count();
    }
    
    /**
     * Mark messages as read for a participant up to a certain point (e.g., now).
     */
    public function markAsRead($participant, ?\Carbon\Carbon $timestamp = null): void
    {
        $timestamp = $timestamp ?? now();
        $relationship = $participant instanceof Admin ? 'adminParticipants' : 'userParticipants';
        $this->{$relationship}()->updateExistingPivot($participant->id, ['last_read_at' => $timestamp]);
    }

    /**
     * Check if a user or admin is a participant in this conversation.
     */
    public function isParticipant($participant): bool
    {
        $relationship = $participant instanceof Admin ? 'adminParticipants' : 'userParticipants';
        return $this->{$relationship}()->where('conversation_user.user_id', $participant->id)->exists();
    }

    /**
     * Get the user who created the conversation.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
