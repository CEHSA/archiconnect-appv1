<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkSubmission;
use App\Events\ClientWorkSubmissionReviewed;
use Illuminate\Support\Facades\Storage; // Added for download
use Illuminate\Validation\Rule; // Added

class WorkSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workSubmissions = WorkSubmission::where('status', WorkSubmission::STATUS_PENDING_CLIENT_REVIEW) // Updated status
            ->whereHas('jobAssignment.job', function ($query) {
                $query->where('user_id', auth()->id());
            })
            ->with(['jobAssignment.job', 'jobAssignment.freelancer'])
            ->latest()
            ->get();

        return view('client.work-submissions.index', compact('workSubmissions'));
    }

    /**
     * Display the specified resource.
     */
    public function show(WorkSubmission $workSubmission)
    {
        // Ensure the authenticated client is authorized to view this submission
        if ($workSubmission->jobAssignment->job->user_id !== auth()->id()) {
            abort(403);
        }

        // Eager load relationships - ensure 'comments' are loaded if needed for the view
        $workSubmission->load(['jobAssignment.job', 'jobAssignment.freelancer', 'comments.user']);

        return view('client.work-submissions.show', compact('workSubmission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, WorkSubmission $workSubmission)
    {
        // Ensure the authenticated client is authorized to update this submission
        if ($workSubmission->jobAssignment->job->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow updating if the status is WorkSubmission::STATUS_PENDING_CLIENT_REVIEW
        if ($workSubmission->status !== WorkSubmission::STATUS_PENDING_CLIENT_REVIEW) {
            return redirect()->route('client.work-submissions.show', $workSubmission)->with('error', 'This submission is not currently awaiting your review.');
        }

        $validatedData = $request->validate([
            'client_status' => ['required', Rule::in([WorkSubmission::STATUS_APPROVED_BY_CLIENT, WorkSubmission::STATUS_CLIENT_REVISION_REQUESTED])], // Use constants
            'client_remarks' => 'nullable|string',
        ]);

        $workSubmission->update([
            'status' => $validatedData['client_status'],
            'client_remarks' => $validatedData['client_remarks'], // This field needs to be added to WorkSubmission model fillable
        ]);

        ClientWorkSubmissionReviewed::dispatch($workSubmission);

        return redirect()->route('client.work-submissions.index')->with('success', 'Work submission review submitted successfully!');
    }

    /**
     * Handle file download for a submission by the client.
     */
    public function download(WorkSubmission $workSubmission)
    {
        // Authorization: Ensure the authenticated user is the client for this job
        if (auth()->id() !== $workSubmission->jobAssignment->job->user_id) {
            abort(403, 'Unauthorized action.');
        }

        // Authorization: Ensure the submission is in a state where client can download
        $allowedStatuses = [
            WorkSubmission::STATUS_PENDING_CLIENT_REVIEW,
            WorkSubmission::STATUS_APPROVED_BY_CLIENT,
            // Potentially add other statuses like client_revision_requested if they should still be able to download
        ];
        if (!in_array($workSubmission->status, $allowedStatuses)) {
            abort(403, 'File is not available for download at this stage.');
        }

        if (!$workSubmission->file_path) {
            abort(404, 'File not found for this submission.');
        }

        // Ensure the file exists on the private disk
        if (!Storage::disk('private')->exists($workSubmission->file_path)) {
            abort(404, 'File not found in storage.');
        }

        return Storage::disk('private')->download($workSubmission->file_path, $workSubmission->original_filename);
    }

    /**
     * Approve a work submission.
     */
    public function approve(WorkSubmission $workSubmission)
    {
        // Ensure the authenticated client is authorized to approve this submission
        if ($workSubmission->jobAssignment->job->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow approval if the status is WorkSubmission::STATUS_PENDING_CLIENT_REVIEW
        if ($workSubmission->status !== WorkSubmission::STATUS_PENDING_CLIENT_REVIEW) {
            return response()->json(['message' => 'This submission is not currently awaiting your approval.'], 400);
        }

        $workSubmission->update([
            'status' => WorkSubmission::STATUS_APPROVED_BY_CLIENT,
            'reviewed_at' => now(),
        ]);

        // Update job status to completed
        $workSubmission->jobAssignment->job->update(['status' => 'completed']);

        event(new ClientWorkSubmissionReviewed($workSubmission));

        return response()->json(['message' => 'Work submission approved successfully!'], 200);
    }

    /**
     * Request revisions for a work submission.
     */
    public function requestRevisions(Request $request, WorkSubmission $workSubmission)
    {
        // Ensure the authenticated client is authorized to request revisions for this submission
        if ($workSubmission->jobAssignment->job->user_id !== auth()->id()) {
            abort(403);
        }

        // Only allow requesting revisions if the status is WorkSubmission::STATUS_PENDING_CLIENT_REVIEW
        if ($workSubmission->status !== WorkSubmission::STATUS_PENDING_CLIENT_REVIEW) {
            return response()->json(['message' => 'This submission is not currently awaiting your review for revisions.'], 400);
        }

        $validatedData = $request->validate([
            'client_remarks' => 'required|string|max:5000',
        ]);

        $workSubmission->update([
            'status' => WorkSubmission::STATUS_CLIENT_REVISION_REQUESTED,
            'client_remarks' => $validatedData['client_remarks'],
            'reviewed_at' => now(),
        ]);

        // Update job status to revisions_requested
        $workSubmission->jobAssignment->job->update(['status' => 'revisions_requested']);

        event(new ClientWorkSubmissionReviewed($workSubmission)); // Or a more specific event if needed

        return response()->json(['message' => 'Revision request submitted successfully!'], 200);
    }
}
