<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Admin; // Added import for Admin model
use App\Models\JobComment; // Added import for JobComment model
use App\Models\JobApplication; // Added

class Job extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'client_id', // Added client_id
        'title',
        'description',
        'budget',
        'skills_required',
        'status',
        'hourly_rate',
        'not_to_exceed_budget',
        'created_by_user_id',
        'created_by_admin_id', // Added created_by_admin_id
        'assigned_freelancer_id',
    ];

    /**
     * Get the user that posted the job.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client user for this job.
     * Assuming client_id refers to a User model.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the proposals for the job.
     */
    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    /**
     * Get the user that created the job.
     */
    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get the assignments for the job.
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(JobAssignment::class);
    }

    /**
     * Get the freelancers assigned to the job.
     */
    public function assignedFreelancers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'job_assignments', 'job_id', 'freelancer_id')
                    ->withPivot('status', 'assigned_by_admin_id', 'freelancer_remarks', 'admin_remarks')
                    ->withTimestamps();
    }

    /**
     * Get the admin that created the job.
     */
    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    /**
     * Get the comments for the job.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(JobComment::class);
    }

    /**
     * Get the freelancer directly assigned to the job.
     */
    public function assignedFreelancer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_freelancer_id');
    }

    /**
     * Get all job applications for the job (potentially through job postings).
     */
    public function jobApplications(): HasMany
    {
        // This assumes applications are directly linked to job_id.
        // If applications are linked via job_postings, a HasManyThrough relationship might be needed
        // or querying via job_postings as done in the controller.
        // For direct access from Job model, if JobApplication has job_id:
        return $this->hasMany(JobApplication::class);
    }
}
