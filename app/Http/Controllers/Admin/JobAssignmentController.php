<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobAssignment;
use App\Models\User;
use App\Models\AssignmentNote; // Added for notes
use App\Models\Conversation; // Added
use App\Events\JobAssigned; // Import the event
use App\Events\JobCompleted; // Added import
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
    public function create(Request $request)
    {
        $jobId = $request->query('job_id'); // Get job_id from query parameter
        if (!$jobId) {
            // Or redirect back with an error, or to a job selection page
            abort(404, 'Job ID is required to create an assignment.');
        }

        $job = Job::findOrFail($jobId); // Find the job or fail

        // Get users with the freelancer role
        $freelancers = User::where('role', User::ROLE_FREELANCER)->orderBy('name')->get();

        // Get users already assigned to this job to potentially exclude them or mark them
        $assignedFreelancerIds = $job->assignments()->pluck('freelancer_id')->toArray();

        return view('admin.jobs.assignments.create', compact('job', 'freelancers', 'assignedFreelancerIds'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'job_id' => ['required', 'exists:jobs,id'], // Ensure job_id is submitted and valid
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
                function ($attribute, $value, $fail) use ($request) {
                    $job = Job::find($request->input('job_id'));
                    if ($job && $job->assignments()->where('freelancer_id', $value)->exists()) {
                        $fail('This freelancer is already assigned to this job.');
                    }
                },
            ],
            'admin_remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        $job = Job::findOrFail($validated['job_id']); // Fetch the job

        $assignment = new JobAssignment();
        $assignment->job_id = $job->id;
        $assignment->freelancer_id = $validated['freelancer_id'];
        $assignment->assigned_by_admin_id = Auth::id();
        $assignment->admin_remarks = $validated['admin_remarks'];
        // Status defaults to 'pending_freelancer_acceptance' via migration

        $assignment->save();

        // Dispatch the event
        event(new JobAssigned($assignment->load(['job.user', 'freelancer', 'assignedByAdmin']))); // Eager load for the listener

        // Find or create a conversation for this assignment and sync participants
        $conversation = Conversation::firstOrCreate(
            ['job_assignment_id' => $assignment->id],
            [
                'job_id' => $assignment->job_id,
                'created_by_user_id' => Auth::id(), // Admin initiated this context
                'subject' => 'Conversation for Assignment: ' . $assignment->job->title,
                'last_message_at' => now(),
            ]
        );
        $participantIds = [
            $assignment->job->user_id, // Client
            $assignment->freelancer_id, // Freelancer
        ];
        $adminUsers = User::where('role', User::ROLE_ADMIN)->pluck('id')->toArray();
        $participantIds = array_unique(array_merge($participantIds, $adminUsers));
        $conversation->participants()->syncWithoutDetaching($participantIds);

        return redirect()->route('admin.jobs.show', $job) // Changed redirect to job show page
                         ->with('success', 'Freelancer assigned, conversation updated, and notification will be sent.');
    }

    /**
     * Display the specified resource.
     */
    public function show($assignmentId) // Changed to accept ID
    {
        $assignment = JobAssignment::findOrFail($assignmentId); // Explicitly find or fail

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
            },
            'assignmentNotes.admin' => function ($query) { // Added for notes, ordered by newest first
                $query->orderBy('created_at', 'desc');
            }
        ]); // Eager load relationships
        return view('admin.jobs.assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($assignmentId) // Changed to accept ID
    {
        $assignment = JobAssignment::findOrFail($assignmentId); // Explicitly find or fail
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

        if ($assignment->job_id) {
            return redirect()->route('admin.jobs.show', $assignment->job_id)
                             ->with('success', 'Assignment updated successfully.');
        } else {
            // Fallback if job_id is somehow null on the assignment
            return redirect()->route('admin.dashboard')
                             ->with('warning', 'Assignment updated, but could not redirect to job details as job reference was missing.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobAssignment $assignment) // Renamed variable
    {
        $jobId = $assignment->job_id; // Store job ID before deleting
        $assignment->delete();

        return redirect()->route('admin.jobs.show', $jobId) // Changed redirect to job show page
                         ->with('success', 'Assignment removed successfully.');
    }

    /**
     * Store a new note for the job assignment.
     */
    public function storeNote(Request $request, JobAssignment $jobAssignment)
    {
        $validated = $request->validate([
            'content' => ['required', 'string', 'max:5000'],
        ]);

        $jobAssignment->assignmentNotes()->create([
            'admin_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        return redirect()->route('admin.job-assignments.show', $jobAssignment)
                         ->with('success', 'Note added successfully.');
    }

    /**
     * Assign a job to a freelancer.
     */
    public function assignJob(Request $request, Job $job)
    {
        $validated = $request->validate([
            'freelancer_id' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    $user = User::find($value);
                    if (!$user || !$user->hasRole(User::ROLE_FREELANCER)) {
                        $fail('The selected user is not a freelancer.');
                    }
                },
            ],
            'assigned_by_admin_id' => ['required', 'exists:users,id'],
            'status' => ['required', 'string', 'in:assigned,pending_freelancer_acceptance'],
        ]);

        // Create the job assignment
        $assignment = JobAssignment::create([
            'job_id' => $job->id,
            'freelancer_id' => $validated['freelancer_id'],
            'assigned_by_admin_id' => $validated['assigned_by_admin_id'],
            'status' => $validated['status'],
        ]);

        // Update the job status and assigned freelancer
        $job->update([
            'assigned_freelancer_id' => $validated['freelancer_id'],
            'status' => $validated['status'],
        ]);

        // Dispatch event (if needed, based on your application's event system)
        // event(new JobAssigned($assignment));

        return response()->json(['message' => 'Job assigned successfully', 'assignment' => $assignment], 200);
    }
}
