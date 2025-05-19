<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobAssignment;
use App\Models\User;
use App\Events\JobAssigned; // Import the event
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Job $job)
    {
        // Eager load relationships for efficiency
        $assignments = $job->assignments()->with(['freelancer', 'assignedByAdmin'])->latest()->paginate(10);

        return view('admin.jobs.assignments.index', compact('job', 'assignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Job $job)
    {
        // Get users with the freelancer role
        $freelancers = User::where('role', User::ROLE_FREELANCER)->orderBy('name')->get();

        // Get users already assigned to this job to potentially exclude them or mark them
        $assignedFreelancerIds = $job->assignments()->pluck('freelancer_id')->toArray();

        return view('admin.jobs.assignments.create', compact('job', 'freelancers', 'assignedFreelancerIds'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Job $job)
    {
        $validated = $request->validate([
            'freelancer_id' => [
                'required',
                'exists:users,id',
                // Ensure the selected user is actually a freelancer
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if (!$user || !$user->hasRole(User::ROLE_FREELANCER)) {
                        $fail('The selected user is not a freelancer.');
                    }
                },
                // Ensure the freelancer is not already assigned to this job
                function ($attribute, $value, $fail) use ($job) {
                    if ($job->assignments()->where('freelancer_id', $value)->exists()) {
                        $fail('This freelancer is already assigned to this job.');
                    }
                },
            ],
            'admin_remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        $assignment = new JobAssignment();
        $assignment->job_id = $job->id;
        $assignment->freelancer_id = $validated['freelancer_id'];
        $assignment->assigned_by_admin_id = Auth::id();
        $assignment->admin_remarks = $validated['admin_remarks'];
        // Status defaults to 'pending_freelancer_acceptance' via migration

        $assignment->save();

        // Dispatch the event
        event(new JobAssigned($assignment->load(['job', 'freelancer', 'assignedByAdmin']))); // Eager load for the listener

        return redirect()->route('admin.jobs.assignments.index', $job)
                         ->with('success', 'Freelancer assigned successfully. Notification will be sent.');
    }

    /**
     * Display the specified resource.
     */
    public function show(JobAssignment $assignment) // Renamed variable for clarity due to shallow nesting
    {
        $assignment->load([
            'job', 
            'freelancer', 
            'assignedByAdmin', 
            'workSubmissions' => function ($query) {
                $query->orderBy('submitted_at', 'desc');
            },
            'taskProgress' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'timeLogs' => function ($query) { // Also load time logs for this assignment
                $query->orderBy('start_time', 'desc');
            },
            'tasks' => function ($query) { // Added tasks relationship for admin view
                $query->orderBy('order', 'asc')->orderBy('created_at', 'asc');
            }
        ]); // Eager load relationships
        return view('admin.jobs.assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobAssignment $assignment) // Renamed variable
    {
        $assignment->load(['job', 'freelancer']); // Eager load relationships
        // Define possible statuses - adjust as needed based on workflow
        $statuses = [
            'pending_freelancer_acceptance' => 'Pending Freelancer Acceptance',
            'accepted' => 'Accepted by Freelancer',
            'declined' => 'Declined by Freelancer',
            'assigned' => 'Assigned (Confirmed)', // Maybe redundant if 'accepted' implies assigned
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ];

        return view('admin.jobs.assignments.edit', compact('assignment', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobAssignment $assignment) // Renamed variable
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending_freelancer_acceptance,accepted,declined,assigned,in_progress,completed,cancelled'], // Validate against defined statuses
            'admin_remarks' => ['nullable', 'string', 'max:5000'],
            'freelancer_remarks' => ['nullable', 'string', 'max:5000'], // Allow admin to update freelancer remarks if needed? Or keep separate?
        ]);

        $assignment->update($validated);

        // Dispatch JobCompleted event if status is set to 'completed'
        if ($assignment->status === 'completed') {
            event(new JobCompleted($assignment->load(['job.client.user', 'freelancer.user']))); // Eager load relationships for the listener
        }

        // TODO: Add other notifications based on status changes (e.g., notify admin if freelancer accepts/declines)

        return redirect()->route('admin.jobs.assignments.index', $assignment->job_id)
                         ->with('success', 'Assignment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobAssignment $assignment) // Renamed variable
    {
        $jobId = $assignment->job_id; // Store job ID before deleting
        $assignment->delete();

        // TODO: Notify freelancer if their assignment is removed?

        return redirect()->route('admin.jobs.assignments.index', $jobId)
                         ->with('success', 'Assignment removed successfully.');
    }
}
