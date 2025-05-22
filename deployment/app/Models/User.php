<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The roles available in the application.
     */
    public const ROLE_ADMIN = 'admin';
    public const ROLE_CLIENT = 'client';
    public const ROLE_FREELANCER = 'freelancer';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_CLIENT,
        self::ROLE_FREELANCER,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Get the client profile associated with the user.
     */
    public function clientProfile(): HasOne
    {
        return $this->hasOne(ClientProfile::class);
    }

    /**
     * Get the freelancer profile associated with the user.
     */
    public function freelancerProfile(): HasOne
    {
        return $this->hasOne(FreelancerProfile::class);
    }

    /**
     * Get the jobs posted by the user (client).
     */
    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class);
    }

    /**
     * Get the proposals submitted by the user (freelancer).
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * Get the job assignments for the user as a freelancer.
     */
    public function jobAssignmentsAsFreelancer(): HasMany
    {
        return $this->hasMany(JobAssignment::class, 'freelancer_id');
    }

    /**
     * Get the job assignments made by the user as an admin.
     */
    public function jobAssignmentsAsAdmin(): HasMany
    {
        return $this->hasMany(JobAssignment::class, 'assigned_by_admin_id');
    }

    /**
     * Get the jobs assigned to the user as a freelancer.
     */
    public function assignedJobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_assignments', 'freelancer_id', 'job_id')
                    ->withPivot('status', 'assigned_by_admin_id', 'freelancer_remarks', 'admin_remarks')
                    ->withTimestamps();
    }

    /**
     * Get the time logs for the user as a freelancer.
     */
    public function timeLogs(): HasMany
    {
        return $this->hasMany(TimeLog::class, 'freelancer_id');
    }

    /**
     * Get the work submissions made by the user as a freelancer.
     */
    public function workSubmissionsAsFreelancer(): HasMany
    {
        return $this->hasMany(WorkSubmission::class, 'freelancer_id');
    }

    /**
     * Get the work submissions reviewed/handled by the user as an admin.
     */
    public function workSubmissionsAsAdmin(): HasMany
    {
        return $this->hasMany(WorkSubmission::class, 'admin_id');
    }

    /**
     * Get the disputes reported by the user.
     */
    public function reportedDisputes(): HasMany
    {
        return $this->hasMany(Dispute::class, 'reporter_id');
    }

    /**
     * Get the disputes reported against the user.
     */
    public function disputesAgainst(): HasMany
    {
        return $this->hasMany(Dispute::class, 'reported_id');
    }

    /**
     * Get the briefing requests submitted by the user (client).
     */
    public function briefingRequests(): HasMany
    {
        return $this->hasMany(BriefingRequest::class, 'client_id');
    }

    /**
     * Get all conversations the user is a participant in.
     */
    public function conversations(): HasMany
    {
        // This retrieves conversations where the user is either participant1 or participant2.
        // Note: This assumes participant types are 'App\Models\User'.
        // If admins have a different model or type, this might need adjustment
        // or a separate relationship for admin-specific conversations.
        return $this->hasMany(Conversation::class, 'participant1_id')
            ->where('participant1_type', self::class) // Ensures it's this User model
            ->orWhere(function ($query) {
                $query->where('participant2_id', $this->id)
                      ->where('participant2_type', self::class);
            });
        // A more robust way if participant_type can vary and you want all conversations
        // regardless of which participant slot the user occupies:
        // return Conversation::where(function ($query) {
        //     $query->where('participant1_id', $this->id)
        //           ->where('participant1_type', self::class);
        // })->orWhere(function ($query) {
        //     $query->where('participant2_id', $this->id)
        //           ->where('participant2_type', self::class);
        // })->orderBy('last_message_at', 'desc');
        // However, for simplicity with existing structure, the above hasMany might be sufficient
        // if the participant_type is consistently App\Models\User for users.
        // Given the Conversation model's morphTo, a direct relationship is tricky.
        // It's often better to query the Conversation model directly using a scope.
        // For now, let's assume we'll fetch conversations in the service/controller
        // and then calculate unread counts.

        // Let's define a more direct way to get conversations for the unread count.
        // This is not a standard Eloquent relationship but a method to fetch them.
    }

    /**
     * Get all conversations this user is a part of.
     * This is a more explicit way to fetch conversations for a user.
     */
    public function getAllConversations()
    {
        return Conversation::where(function ($query) {
                $query->where('participant1_id', $this->id)
                      ->where('participant1_type', $this->getMorphClass());
            })
            ->orWhere(function ($query) {
                $query->where('participant2_id', $this->id)
                      ->where('participant2_type', $this->getMorphClass());
            })
            ->orderBy('last_message_at', 'desc')
            ->get();
    }


    /**
     * Get the total number of unread messages for the user.
     */
    public function totalUnreadMessagesCount(): int
    {
        $totalUnread = 0;
        // Fetch all conversations the user is part of
        $conversations = Conversation::where(function ($query) {
            $query->where('participant1_id', $this->id)
                  ->where('participant1_type', $this->getMorphClass()); // Use morph class for accuracy
        })->orWhere(function ($query) {
            $query->where('participant2_id', $this->id)
                  ->where('participant2_type', $this->getMorphClass());
        })->with('messages')->get(); // Eager load messages

        foreach ($conversations as $conversation) {
            $totalUnread += $conversation->unreadCount($this);
        }
        return $totalUnread;
    }
}
