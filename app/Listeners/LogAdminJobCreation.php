<?php

namespace App\Listeners;

use App\Events\AdminJobPosted;
use App\Models\AdminActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LogAdminJobCreation
{
    public function __construct()
    {
        //
    }

    public function handle(AdminJobPosted $event): void
    {
        $admin = $event->adminUser ?? Auth::guard('admin')->user(); // Get admin from event or Auth

        if (!$admin) {
            Log::warning('LogAdminJobCreation: Could not determine admin user for AdminJobPosted event.', ['job_id' => $event->job->id]);
            return;
        }

        $job = $event->job;

        AdminActivityLog::create([
            'admin_id' => $admin->id,
            'action_type' => 'job_created',
            'description' => "Admin {$admin->name} created a new job: {$job->title} (ID: {$job->id})",
            'loggable_id' => $job->id,
            'loggable_type' => get_class($job),
        ]);

        Log::info("Admin job creation logged for job ID: {$job->id} by admin ID: {$admin->id}");
    }
}
