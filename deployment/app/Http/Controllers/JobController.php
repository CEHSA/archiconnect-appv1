<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreJobRequest;
use App\Http\Requests\UpdateJobRequest;
use App\Models\Job;
use App\Models\JobApplication; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $jobs = Auth::user()->jobs()->latest()->paginate(10);
        return view('client.jobs.index', compact('jobs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('client.jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Auth::user()->jobs()->create($validated);

        return redirect()->route('client.jobs.index')->with('success', 'Job posted successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job): View
    {
        // Ensure the authenticated user owns this job
        if (Auth::id() !== $job->user_id) {
            abort(403);
        }
        return view('client.jobs.show', compact('job'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job): View
    {
        // Ensure the authenticated user owns this job
        if (Auth::id() !== $job->user_id) {
            abort(403);
        }
        return view('client.jobs.edit', compact('job'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequest $request, Job $job): RedirectResponse
    {
        $validated = $request->validated();

        $job->update($validated);

        return redirect()->route('client.jobs.index')->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job): RedirectResponse
    {
        // Ensure the authenticated user owns this job
        if (Auth::id() !== $job->user_id) {
            abort(403);
        }

        $job->delete();

        return redirect()->route('client.jobs.index')->with('success', 'Job deleted successfully.');
    }

    /**
     * Display a listing of available jobs for freelancers to browse.
     */
    public function browse(): View
    {
        // Get all open jobs, ordered by most recent first
        // Ensure that the user associated with the job has a clientProfile to prevent errors
        $jobs = Job::where('status', 'open')
            ->whereHas('user.clientProfile') // Ensures that user relation exists and has a clientProfile
            ->with(['user.clientProfile']) // Eager load client info
            ->latest()
            ->paginate(10);

        return view('freelancer.jobs.browse', compact('jobs'));
    }

    /**
     * Display a listing of applications for a specific job (client view).
     */
    public function jobApplications(Job $job): View
    {
        $this->authorize('view', $job); // Ensure client owns the job

        // Fetch applications related to this job through JobPostings
        // This assumes applications are made to JobPostings, which are linked to the Job.
        $applications = JobApplication::whereHas('jobPosting', function ($query) use ($job) {
            $query->where('job_id', $job->id);
        })->with(['freelancer.freelancerProfile', 'jobPosting'])->latest('submitted_at')->get();

        return view('client.jobs.applications', compact('job', 'applications'));
    }
}
