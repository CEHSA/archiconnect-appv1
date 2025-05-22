<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\JobPosting;
use App\Http\Requests\StoreJobApplicationRequest;
use App\Http\Requests\UpdateJobApplicationRequest;
use App\Events\JobApplicationSubmitted; // Added
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class JobApplicationController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(int $job_posting_id): View
    {
        $jobPosting = JobPosting::with('job.user')->findOrFail($job_posting_id);
        $job = $jobPosting->job;

        // Ensure the freelancer hasn't already applied to this specific posting
        $existingApplication = JobApplication::where('job_posting_id', $jobPosting->id)
                                            ->where('freelancer_id', Auth::id())
                                            ->first();
        if ($existingApplication) {
            // Or redirect with a message, or to an edit page if allowed
            abort(403, 'You have already applied for this job posting.'); 
        }

        return view('freelancer.job-applications.create', compact('jobPosting', 'job'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobApplicationRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        
        $jobPosting = JobPosting::findOrFail($validatedData['job_posting_id']);

        // Ensure the freelancer hasn't already applied (double check)
        $existingApplication = JobApplication::where('job_posting_id', $jobPosting->id)
                                            ->where('freelancer_id', Auth::id())
                                            ->first();
        if ($existingApplication) {
            return redirect()->route('freelancer.jobs.show', $jobPosting->job_id)
                             ->with('info', 'You have already applied for this job posting.');
        }

        $application = JobApplication::create([
            'job_posting_id' => $jobPosting->id,
            'freelancer_id' => Auth::id(),
            'job_id' => $jobPosting->job_id,
            'cover_letter' => $validatedData['cover_letter'],
            'proposed_rate' => $validatedData['proposed_rate'] ?? null,
            'estimated_timeline' => $validatedData['estimated_timeline'] ?? null,
            'status' => 'submitted',
            // submitted_at is handled by database default
        ]);

        event(new JobApplicationSubmitted($application));

        return redirect()->route('freelancer.jobs.show', $jobPosting->job_id)
                         ->with('success', 'Your application has been submitted successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(JobApplication $jobApplication)
    {
        // Optional: Implement if freelancers need to view their submitted applications
        // Ensure authorization: $this->authorize('view', $jobApplication);
        // return view('freelancer.job-applications.show', compact('jobApplication'));
        return redirect()->route('freelancer.dashboard'); // Placeholder
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobApplication $jobApplication)
    {
        // Optional: Implement if applications can be edited after submission
        // Ensure authorization: $this->authorize('update', $jobApplication);
        // $jobPosting = $jobApplication->jobPosting()->with('job.user')->firstOrFail();
        // $job = $jobPosting->job;
        // return view('freelancer.job-applications.edit', compact('jobApplication', 'jobPosting', 'job'));
        return redirect()->route('freelancer.dashboard'); // Placeholder
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobApplicationRequest $request, JobApplication $jobApplication)
    {
        // Optional: Implement if applications can be edited
        // Ensure authorization: $this->authorize('update', $jobApplication);
        // $validatedData = $request->validated();
        // $jobApplication->update($validatedData);
        // return redirect()->route('freelancer.job-applications.show', $jobApplication)->with('success', 'Application updated.');
        return redirect()->route('freelancer.dashboard'); // Placeholder
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobApplication $jobApplication)
    {
        // Optional: Implement if freelancers can withdraw applications
        // Ensure authorization: $this->authorize('delete', $jobApplication);
        // $job_id = $jobApplication->job_id;
        // $jobApplication->delete();
        // return redirect()->route('freelancer.jobs.show', $job_id)->with('success', 'Application withdrawn.');
        return redirect()->route('freelancer.dashboard'); // Placeholder
    }
}
