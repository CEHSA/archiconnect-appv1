<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobAssignment; // Added
use App\Http\Requests\StoreJobRequest; // This might not be needed if not using store
use App\Http\Requests\UpdateJobRequest; // This might not be needed if not using update
use Illuminate\Support\Facades\Auth; // Added
use Illuminate\Http\RedirectResponse; // Added
use App\Events\JobAcceptanceRequested; // Added - Will create this event next
use App\Models\JobPosting; // Already present or re-added if it was missing
use Illuminate\Http\Request; // Added for Request object
use Illuminate\View\View; // Added for View return type

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        // Ensure the job's user (client) and their profile are loaded and valid.
        // This helps prevent errors if a job record has an invalid user_id
        // or if the user (client) doesn't have a clientProfile.
        $job->load(['user.clientProfile', 'proposals']);

        if (!$job->user || !$job->user->clientProfile) {
            // This job is missing critical client information to be displayed.
            // Log this issue for investigation if it occurs.
            // Log::error("Job ID {$job->id} is missing user or clientProfile for freelancer view.");
            abort(404, 'Job client information is incomplete or the job is not properly configured.');
        }

        // Check if this job was specifically posted to the current freelancer
        $jobPosting = JobPosting::where('job_id', $job->id)
                                ->where('freelancer_id', Auth::id())
                                ->first();
        
        return view('freelancer.jobs.show', compact('job', 'jobPosting'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequest $request, Job $job)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        //
    }

    /**
     * Display a listing of jobs posted to the authenticated freelancer.
     */
    public function postedJobsIndex(Request $request): View
    {
        $freelancer = Auth::user();
        $postedJobIds = JobPosting::where('freelancer_id', $freelancer->id)
                                ->pluck('job_id');

        $jobs = Job::whereIn('id', $postedJobIds)
                    ->where('status', 'open') // Only show open jobs, or adjust as needed
                    ->with(['user.clientProfile']) // Eager load client info
                    ->latest('updated_at') // Or 'posted_at' from JobPosting if joined
                    ->paginate(10);

        return view('freelancer.jobs.posted-index', compact('jobs')); // New view
    }

    /**
     * Allow a freelancer to accept a job.
     */
    public function acceptJob(Job $job): RedirectResponse
    {
        $freelancer = Auth::user();

        // 1. Verify the job is still 'open'
        if ($job->status !== 'open') {
            return redirect()->route('freelancer.jobs.show', $job)->with('error', 'This job is no longer open for acceptance.');
        }

        // 2. Check if this freelancer has already been assigned or requested this job
        $existingAssignment = JobAssignment::where('job_id', $job->id)
            ->where('freelancer_id', $freelancer->id)
            ->first();

        if ($existingAssignment) {
            return redirect()->route('freelancer.jobs.show', $job)->with('info', 'You have already accepted or been assigned this job.');
        }
        
        // 3. Create a JobAssignment
        // We need the client_id from the job's original poster (user_id on Job model)
        $jobAssignment = JobAssignment::create([
            'job_id' => $job->id,
            'client_id' => $job->user_id, // Assuming job->user_id is the client who posted it
            'freelancer_id' => $freelancer->id,
            'status' => 'pending_admin_approval', // New status
            // 'assigned_by_admin_id' will be null initially, admin approves later
        ]);

        // 4. Update the Job status
        $job->status = 'pending_admin_approval'; // New status
        $job->save();

        // 5. Dispatch an event for admin notification
        event(new JobAcceptanceRequested($job, $jobAssignment, $freelancer));

        return redirect()->route('freelancer.jobs.show', $job)->with('success', 'Job accepted! Waiting for admin approval.');
    }
}
