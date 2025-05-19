<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkSubmission; // Add this line
use App\Events\ClientWorkSubmissionReviewed; // Add this line

class ClientWorkSubmissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workSubmissions = WorkSubmission::where('status', 'ready_for_client_review')
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

        $workSubmission->load(['jobAssignment.job', 'jobAssignment.freelancer', 'timeLogs']); // Eager load relationships

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

        // Only allow updating if the status is 'ready_for_client_review'
        if ($workSubmission->status !== 'ready_for_client_review') {
            return redirect()->route('client.work-submissions.show', $workSubmission)->with('error', 'This submission is not currently awaiting your review.');
        }

        $validatedData = $request->validate([
            'client_status' => 'required|in:approved_by_client,needs_revision_by_client',
            'client_remarks' => 'nullable|string',
        ]);

        $workSubmission->update([
            'status' => $validatedData['client_status'],
            'client_remarks' => $validatedData['client_remarks'],
        ]);

        ClientWorkSubmissionReviewed::dispatch($workSubmission);

        return redirect()->route('client.work-submissions.index')->with('success', 'Work submission review submitted successfully!');
    }
}
