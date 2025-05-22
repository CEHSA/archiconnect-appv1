<?php

namespace App\Providers;

use App\Models\Job;
use App\Models\Proposal;
use App\Models\User;
use App\Policies\JobPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Job::class => JobPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        // Job proposal viewing (client only)
        Gate::define('view-proposals', function (User $user, Job $job) {
            return $user->id === $job->user_id;
        });

        // Proposal viewing
        Gate::define('view', function (User $user, Proposal $proposal) {
            return $user->id === $proposal->user_id || // Freelancer who made the proposal
                   $user->id === $proposal->job->user_id; // Client who owns the job
        });

        // Proposal status updates (client only)
        Gate::define('update-status', function (User $user, Proposal $proposal) {
            return $user->id === $proposal->job->user_id;
        });
    }
}
