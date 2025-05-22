<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreJobRequest;
use App\Http\Requests\Admin\UpdateJobRequest;
use App\Models\Job;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Import Log facade
use Illuminate\Validation\Rule;
use App\Events\AdminJobPosted;
use Illuminate\Database\QueryException; // Import QueryException

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Job::with(['user', 'createdByAdmin']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date (assuming 'created_at' for simplicity, adjust if another date field is meant)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filter by client (user_id on jobs table)
        if ($request->filled('client_id')) {
            $query->where('user_id', $request->client_id);
        }

        $jobs = $query->latest()->paginate(10)->withQueryString();

        // Data for filter dropdowns
        $statuses = Job::select('status')->distinct()->orderBy('status')->pluck('status');
        $clients = User::where('role', User::ROLE_CLIENT)
                        ->whereIn('id', Job::select('user_id')->distinct()) // Only clients who have posted jobs
                        ->orderBy('name')
                        ->pluck('name', 'id');

        return view('admin.jobs.index', compact('jobs', 'statuses', 'clients', 'request'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clients = User::where('role', User::ROLE_CLIENT)->orderBy('name')->get();
        $freelancers = User::where('role', User::ROLE_FREELANCER)->orderBy('name')->get(); // Assuming User::ROLE_FREELANCER exists
        return view('admin.jobs.create', compact('clients', 'freelancers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreJobRequest $request)
    {
        $validatedData = $request->validated();
        $adminUser = Auth::guard('admin')->user();
        $validatedData['created_by_admin_id'] = $adminUser ? $adminUser->id : null;

        $job = Job::create($validatedData);

        // Dispatch the event after the job is created
        if ($adminUser) {
            event(new AdminJobPosted($job, $adminUser));
        } else {
            // Fallback if admin user somehow not found, though guard should prevent this action
            event(new AdminJobPosted($job));
        }

        return redirect()->route('admin.jobs.index')->with('success', 'Job created successfully and freelancers notified.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        $job->load(['user', 'createdByAdmin', 'assignments', 'proposals', 'comments']);
        return view('admin.jobs.show', compact('job'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job)
    {
        $clients = User::where('role', User::ROLE_CLIENT)->orderBy('name')->get();
        $freelancers = User::where('role', User::ROLE_FREELANCER)->orderBy('name')->get(); // Assuming User::ROLE_FREELANCER exists
        return view('admin.jobs.edit', compact('job', 'clients', 'freelancers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateJobRequest $request, Job $job)
    {
        $validatedData = $request->validated();

        $job->update($validatedData);

        return redirect()->route('admin.jobs.index')->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        try {
            $job->delete();
            return redirect()->route('admin.jobs.index')->with('success', 'Job deleted successfully.');
        } catch (QueryException $e) {
            // Log the detailed SQL error
            Log::error("Failed to delete job ID {$job->id}: " . $e->getMessage());
            // Provide a more informative error message if possible, or keep it generic
            $errorMessage = 'Failed to delete job. It might be associated with other records.';
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                $errorMessage .= ' Please ensure all related data (like assignments, proposals, comments, etc.) are handled or removed.';
            }
            return redirect()->route('admin.jobs.index')->with('error', $errorMessage);
        } catch (\Exception $e) {
            // Log any other generic error
            Log::error("Generic error deleting job ID {$job->id}: " . $e->getMessage());
            return redirect()->route('admin.jobs.index')->with('error', 'An unexpected error occurred while trying to delete the job.');
        }
    }

    /**
     * Show the form for posting a job to freelancers.
     */
    public function postToFreelancers(Request $request, Job $job)
    {
        $query = User::where('role', User::ROLE_FREELANCER)
                     ->with(['freelancerProfile', 'timeLogs']); // Eager load profile and time logs

        // Apply filters
        if ($request->filled('filter_availability')) {
            $query->whereHas('freelancerProfile', function ($q) use ($request) {
                $q->where('availability', $request->filter_availability);
            });
        }

        if ($request->filled('filter_skills')) {
            $skills = array_map('trim', explode(',', $request->filter_skills));
            $query->whereHas('freelancerProfile', function ($q) use ($skills) {
                foreach ($skills as $skill) {
                    $q->where('skills', 'LIKE', '%' . $skill . '%');
                }
            });
        }

        if ($request->filled('filter_experience')) {
            $query->whereHas('freelancerProfile', function ($q) use ($request) {
                $q->where('experience_level', $request->filter_experience);
            });
        }

        $freelancers = $query->orderBy('name')->get()->map(function ($freelancer) {
            // Calculate total hours logged
            $totalSeconds = $freelancer->timeLogs->sum(function ($log) {
                if ($log->start_time && $log->end_time) {
                    return \Carbon\Carbon::parse($log->end_time)->diffInSeconds(\Carbon\Carbon::parse($log->start_time));
                }
                return 0;
            });
            $freelancer->total_hours_logged = round($totalSeconds / 3600, 2); // Convert seconds to hours

            // Determine busy status (simplified: has active assignments)
            // This is a placeholder; a more robust 'busy' status would be needed.
            // For now, we'll use the 'availability' field from FreelancerProfile if it's reliable.
            // If not, we might check for active JobAssignments.
            // $freelancer->is_busy = $freelancer->jobAssignmentsAsFreelancer()->whereIn('status', ['in_progress', 'pending_approval'])->exists();
            return $freelancer;
        });
        
        // Pass filter values back to the view to repopulate fields
        $filters = $request->only(['filter_availability', 'filter_skills', 'filter_experience']);

        return view('admin.jobs.post-to-freelancers', compact('job', 'freelancers', 'filters'));
    }

    /**
     * Store job postings for selected freelancers.
     */
    public function sendPostings(Request $request, Job $job)
    {
        $request->validate([
            'freelancer_ids' => 'required|array',
            'freelancer_ids.*' => 'exists:users,id,role,' . User::ROLE_FREELANCER,
        ]);

        $freelancerIds = $request->input('freelancer_ids');
        $now = now();
        $postings = [];

        foreach ($freelancerIds as $freelancerId) {
            // Use updateOrCreate to avoid duplicate entries if the form is submitted multiple times
            // or if a job was already posted to a freelancer.
            // The unique constraint on job_id and freelancer_id in the migration handles DB level uniqueness.
            $posting = \App\Models\JobPosting::updateOrCreate(
                ['job_id' => $job->id, 'freelancer_id' => $freelancerId],
                ['posted_at' => $now]
            );
            $postings[] = $posting;
        }

        // Optionally, update the job status to indicate it has been posted, if applicable
        // For example: $job->update(['status' => 'open']); // Or a specific 'posted_to_freelancers' status

        // Dispatch an event (optional, for notifications or other actions)
        event(new \App\Events\JobPostedToFreelancers($job, $freelancerIds));

        return redirect()->route('admin.jobs.show', $job)->with('success', 'Job successfully posted to selected freelancers.');
    }

    /**
     * Display a listing of current/active jobs.
     */
    public function currentJobs(Request $request)
    {
        // Define what constitutes a "current" job status
        $currentStatuses = ['in_progress', 'pending_freelancer_acceptance', 'pending_admin_approval', 'open', 'under_review', 'needs_revision', 'client_revision_requested', 'admin_revision_requested']; // Add other relevant active statuses

        $query = Job::with(['user', 'createdByAdmin', 'assignedFreelancer.freelancerProfile', 'assignments.tasks', 'assignments.freelancer'])
                    ->whereIn('status', $currentStatuses);

        // You can add filters similar to the index method if needed for this page too
        // For example, filter by assigned freelancer:
        if ($request->filled('assigned_freelancer_id')) {
            $query->where('assigned_freelancer_id', $request->assigned_freelancer_id);
        }
        // Filter by client:
        if ($request->filled('client_id')) {
            $query->where('user_id', $request->client_id);
        }


        $jobs = $query->latest()->paginate(10)->withQueryString();
        
        // Data for filter dropdowns
        $clients = User::where('role', User::ROLE_CLIENT)
                        ->whereIn('id', Job::select('user_id')->distinct()->whereIn('status', $currentStatuses))
                        ->orderBy('name')
                        ->pluck('name', 'id');

        $freelancers = User::where('role', User::ROLE_FREELANCER)
                            ->whereIn('id', Job::select('assigned_freelancer_id')->distinct()->whereIn('status', $currentStatuses))
                            ->orderBy('name')
                            ->pluck('name', 'id');


        return view('admin.jobs.current', compact('jobs', 'request', 'clients', 'freelancers'));
    }
}
