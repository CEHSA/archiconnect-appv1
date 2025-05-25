<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_activity_at',
    ];

    public const ROLE_ADMIN = 'admin';
    public const ROLE_CLIENT = 'client';
    public const ROLE_FREELANCER = 'freelancer';

    public const ROLES = [
        self::ROLE_ADMIN,
        self::ROLE_CLIENT,
        self::ROLE_FREELANCER,
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_activity_at' => 'datetime',
        ];
    }

    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isClient(): bool
    {
        return $this->hasRole(self::ROLE_CLIENT);
    }

    public function isFreelancer(): bool
    {
        return $this->hasRole(self::ROLE_FREELANCER);
    }

    public function clientProfile(): HasOne
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function freelancerProfile(): HasOne
    {
        return $this->hasOne(FreelancerProfile::class);
    }

    public function jobs(): HasMany
    {
        return $this->hasMany(Job::class, 'client_id');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'freelancer_id');
    }

    public function jobAssignmentsAsFreelancer(): HasMany
    {
        return $this->hasMany(JobAssignment::class, 'freelancer_id');
    }

    public function jobAssignmentsAsAdmin(): HasMany
    {
        return $this->hasMany(JobAssignment::class, 'assigned_by_admin_id');
    }

    public function assignedJobs(): BelongsToMany
    {
        return $this->belongsToMany(Job::class, 'job_assignments', 'freelancer_id', 'job_id')
                    ->withPivot('status', 'assigned_by_admin_id', 'freelancer_remarks', 'admin_remarks')
                    ->withTimestamps();
    }

    public function briefingRequests(): HasMany
    {
        return $this->hasMany(BriefingRequest::class, 'client_id');
    }

    public function isOnline(): bool
    {
        return $this->last_activity_at && $this->last_activity_at->greaterThanOrEqualTo(now()->subMinutes(5));
    }
}
