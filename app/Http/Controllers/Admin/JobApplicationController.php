<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Job; // Added
use App\Models\User; // Added
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Events\JobApplicationStatusUpdated;
use App\Models\JobAssignment; // Added
use App\Events\JobAssigned; // Added
use App\Models\Conversation; // Added
// Removed duplicate: use App\Models\Job; 

class JobApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = JobApplication::with(['job', 'freelancer.freelancerProfile', 'jobPosting']);

        // Filtering examples (can be expanded)
        if ($request->filled('job_id')) {
            $query->where('job_id', $request->input('job_id'));
        }
        if ($request->filled('freelancer_id')) {
            $query->where('freelancer_id', $request->input('freelancer_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $applications = $query->latest('submitted_at')->paginate(15)->withQueryString();

        // Data for filters
        $jobs = Job::orderBy('title')->get(['id', 'title']);
        $freelancers = User::where('role', User::ROLE_FREELANCER)->orderBy('name')->get(['id', 'name']);

        return view('admin.job-applications.index', compact('applications', 'jobs', 'freelancers'));
    }

    /**
     * Display the specified resource.
     */
    public function show(JobApplication $application): View // Changed variable name to $application for clarity
    {
        $application->load(['job.user', 'freelancer.freelancerProfile', 'jobPosting']);
        return view('admin.job-applications.show', compact('application'));
    }

    /**
     * Update the status of the specified resource in storage.
     */
    public function updateStatus(Request $request, JobApplication $application): RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'string', 'in:submitted,viewed,shortlisted,rejected,accepted_for_assignment'], // Add more statuses as needed
        ]);

        $oldStatus = $application->status;
        $application->status = $request->input('status');
        $application->save();

        event(new JobApplicationStatusUpdated($application, $oldStatus));

        if ($application->status === 'accepted_for_assignment') {
            // Create or update JobAssignment
            $jobAssignment = JobAssignment::updateOrCreate(
                ['job_id' => $application->job_id, 'freelancer_id' => $application->freelancer_id],
                [
                    'client_id' => $application->job->user_id, // Client who posted the job
                    'assigned_by_admin_id' => auth()->guard('admin')->id(), // Current admin
                    'status' => 'pending_freelancer_acceptance', // Or 'active' if direct assignment
                    'rate' => $application->proposed_rate ?? $application->job->hourly_rate ?? $application->job->budget, // Prioritize proposed rate
                    // 'estimated_completion_date' => // Potentially from application or job
                ]
            );

            // Update parent Job status
            $job = $application->job;
            $job->status = 'assigned'; // Or 'in_progress' depending on workflow
            $job->assigned_freelancer_id = $application->freelancer_id; // Set the assigned freelancer on the job
            $job->save();

            // Optionally, update other applications for this job
            JobApplication::where('job_id', $application->job_id)
                ->where('id', '!=', $application->id)
                ->where('status', '!=', 'rejected') // Don't update already rejected ones
                ->update(['status' => 'job_filled']); // New status for other applications

            // Dispatch JobAssigned event
            event(new JobAssigned($jobAssignment));

            // Find or create a conversation for this assignment and sync participants
            $conversation = Conversation::firstOrCreate(
                ['job_assignment_id' => $jobAssignment->id],
                [
                    'job_id' => $jobAssignment->job_id,
                    'created_by_user_id' => auth()->guard('admin')->id(), // Admin initiated this context
                    'subject' => 'Conversation for Assignment: ' . $jobAssignment->job->title,
                    'last_message_at' => now(),
                ]
            );
            $participantIds = [
                $jobAssignment->client_id, // Client
                $jobAssignment->freelancer_id, // Freelancer
            ];
            $adminUsers = User::where('role', User::ROLE_ADMIN)->pluck('id')->toArray();
            $participantIds = array_unique(array_merge($participantIds, $adminUsers));
            $conversation->participants()->syncWithoutDetaching($participantIds);
            
            return redirect()->route('admin.job-applications.show', $application)
                             ->with('success', 'Application accepted, job assigned, conversation updated, and other applications updated.');
        }

        return redirect()->route('admin.job-applications.show', $application)
                         ->with('success', 'Application status updated successfully.');
    }

    // Note: Standard store, edit, update, destroy methods are not implemented
    // as applications are created by freelancers, and admin actions are primarily status updates.
    // These can be added if full CRUD by admin is required.
}
