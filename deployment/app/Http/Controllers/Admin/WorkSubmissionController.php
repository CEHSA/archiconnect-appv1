<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WorkSubmissionController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(WorkSubmission $submission)
    {
        $submission->load(['jobAssignment.job', 'freelancer', 'admin']);
        // Also load related time logs for the assignment for context
        $assignmentTimeLogs = $submission->jobAssignment->timeLogs()->orderBy('start_time', 'desc')->get();

        return view('admin.submissions.show', compact('submission', 'assignmentTimeLogs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(WorkSubmission $submission)
    {
        $submission->load(['jobAssignment.job', 'freelancer']);
        $statuses = ['submitted', 'under_review', 'needs_revision', 'approved_by_admin', 'cancelled']; // Define available statuses

        return view('admin.submissions.edit', compact('submission', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkSubmission $submission)
    {
        $statuses = ['submitted', 'under_review', 'needs_revision', 'approved_by_admin', 'cancelled'];
        $validated = $request->validate([
            'status' => ['required', Rule::in($statuses)],
            'admin_remarks' => ['nullable', 'string', 'max:5000'],
        ]);

        $submission->status = $validated['status'];
        $submission->admin_remarks = $validated['admin_remarks'];
        $submission->admin_id = Auth::id(); // Track which admin updated it
        $submission->reviewed_at = now();
        $submission->save();

        // Dispatch event to notify freelancer if admin requests revisions
        if ($validated['status'] === \App\Models\WorkSubmission::STATUS_ADMIN_REVISION_REQUESTED) {
             event(new \App\Events\WorkSubmissionReviewedByAdmin($submission)); // Assuming this event is suitable for revision requests
        }

        // Dispatch event if admin submits to client
        if ($validated['status'] === \App\Models\WorkSubmission::STATUS_PENDING_CLIENT_REVIEW) {
            event(new \App\Events\WorkSubmissionSubmittedToClient($submission));
            // Potentially also fire WorkSubmissionReviewedByAdmin if it signifies admin's approval before client review
            // event(new \App\Events\WorkSubmissionReviewedByAdmin($submission));
        }

        // If 'approved_by_client' (final approval), consider updating JobAssignment status to 'completed' or similar
        if ($validated['status'] === \App\Models\WorkSubmission::STATUS_APPROVED_BY_CLIENT) {
            // Potentially update $submission->jobAssignment->status here
            // This logic might be more complex (e.g., all submissions approved? final submission?)
        }

        return redirect()->route('admin.submissions.show', $submission->id)
                         ->with('success', 'Work submission updated successfully.');
    }

    /**
     * Handle file download for a submission.
     */
    public function download(WorkSubmission $submission)
    {
        if (!$submission->file_path) {
            abort(404, 'File not found for this submission.');
        }

        // Ensure the file exists on the private disk
        if (!Storage::disk('private')->exists($submission->file_path)) {
            abort(404, 'File not found in storage.');
        }

        return response()->download(Storage::disk('private')->path($submission->file_path), $submission->original_filename);
    }

    // Index, Create, Store, Destroy methods are not typically needed for admin review of submissions
    // as submissions are created by freelancers. Listing might be part of JobAssignment show page.
    public function index() { abort(404); }
    public function create() { abort(404); }
    public function store(Request $request) { abort(404); }
    public function destroy(WorkSubmission $workSubmission) { abort(404); }
}
