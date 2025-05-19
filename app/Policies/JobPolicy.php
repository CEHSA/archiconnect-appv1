<?php

namespace App\Policies;

use App\Models\Job;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JobPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view jobs
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Job $job): bool
    {
        // Admin can view any job
        if ($user->role === 'admin') {
            return true;
        }

        // Client can view their own jobs
        if ($user->role === 'client') {
            return $user->id === $job->user_id;
        }

        // Freelancer can view open jobs
        if ($user->role === 'freelancer') {
            return $job->status === 'open';
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only clients and admins can create jobs
        return $user->role === 'client' || $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Job $job): bool
    {
        // Admin can update any job
        if ($user->role === 'admin') {
            return true;
        }

        // Client can update their own jobs
        if ($user->role === 'client') {
            return $user->id === $job->user_id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Job $job): bool
    {
        // Admin can delete any job
        if ($user->role === 'admin') {
            return true;
        }

        // Client can delete their own jobs
        if ($user->role === 'client') {
            return $user->id === $job->user_id;
        }

        return false;
    }
}
