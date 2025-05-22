<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\JobAssignment;
use App\Models\TimeLog; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class JobAssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = JobAssignment::where('freelancer_id', Auth::id())
                                    ->with(['job', 'assignedByAdmin'])
                                    ->latest()
                                    ->paginate(10);

        return view('freelancer.assignments.index', compact('assignments'));
    }

    /**
     * Display the specified resource.
     */
    public function show(JobAssignment $assignment)
    {
        // Ensure the freelancer owns this assignment
        if ($assignment->freelancer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $assignment->load([
            'job',
            'assignedByAdmin',
            'workSubmissions' => function ($query) {
                $query->orderBy('submitted_at', 'desc');
            },
            'taskProgress' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'disputes' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'conversations', // Temporarily simplified to test base relationship
            // 'conversations.messages.attachments',
            'budgetAppeals' => function ($query) {
                $query->orderBy('created_at', 'desc');
            },
            'tasks' => function ($query) { // Added tasks relationship
                $query->orderBy('order', 'asc')->orderBy('created_at', 'asc');
            },
        ]);

        // Get all time logs for this specific assignment, ordered by start time
        $assignmentTimeLogs = TimeLog::where('job_assignment_id', $assignment->id)
                                    ->where('freelancer_id', Auth::id())
                                    ->orderBy('start_time', 'desc')
                                    ->get();

        // Get the current active time log for the freelancer (if any, across all assignments)
        $activeTimeLog = TimeLog::where('freelancer_id', Auth::id())
                                ->whereNull('end_time')
                                ->first();

        return view('freelancer.assignments.show', compact('assignment', 'assignmentTimeLogs', 'activeTimeLog'));
    }

    /**
     * Update the status of the specified assignment (e.g., accept/decline).
     */
    public function updateStatus(Request $request, JobAssignment $assignment)
    {
        // Ensure the freelancer owns this assignment
        if ($assignment->freelancer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the new status and remarks
        $validated = $request->validate([
            'status' => [
                'required',
                'string',
                Rule::in(['accepted', 'declined']), // Freelancer can only accept or decline initially
            ],
            'freelancer_remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        // Prevent updating status if it's no longer 'pending_freelancer_acceptance'
        if ($assignment->status !== 'pending_freelancer_acceptance') {
            return redirect()->route('freelancer.assignments.show', $assignment)
                             ->with('error', 'This assignment can no longer be updated.');
        }

        $assignment->status = $validated['status'];
        $assignment->freelancer_remarks = $validated['freelancer_remarks'];
        $assignment->save();

        // Dispatch an event to notify the admin of the freelancer's response
        event(new \App\Events\FreelancerRespondedToAssignment($assignment->load(['job', 'freelancer', 'assignedByAdmin']))); // Eager load for the listener

        return redirect()->route('freelancer.assignments.show', $assignment)
                         ->with('success', 'Assignment status updated successfully.');
    }

    // Remove unused CRUD methods if this controller is not a full resource controller
    // For now, keeping them empty.
    public function create() { abort(404); }
    public function store(Request $request) { abort(404); }
    public function edit(JobAssignment $jobAssignment) { abort(404); }
    public function update(Request $request, JobAssignment $jobAssignment) { abort(404); }
    public function destroy(JobAssignment $jobAssignment) { abort(404); }
}
