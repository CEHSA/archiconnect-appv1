<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dispute;
use App\Models\DisputeUpdate; // Import the new model
use App\Events\DisputeUpdatedByAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth; // Import Auth facade

class DisputeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Eager load relationships for efficiency
        $disputes = Dispute::with(['jobAssignment.job', 'reporter', 'reportedUser'])
                            ->orderBy('created_at', 'desc')
                            ->paginate(15);
        return view('admin.disputes.index', compact('disputes'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Dispute $dispute)
    {
        $dispute->load(['jobAssignment.job', 'reporter', 'reportedUser', 'jobAssignment.freelancer.freelancerProfile', 'jobAssignment.job.client.clientProfile', 'updates.user']); // Eager load updates and the user who made the update
        return view('admin.disputes.show', compact('dispute'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dispute $dispute)
    {
        $dispute->load(['jobAssignment.job', 'reporter', 'reportedUser']);
        $statuses = ['open', 'under_review', 'awaiting_client_input', 'awaiting_freelancer_input', 'resolved', 'closed_resolved', 'closed_unresolved']; // Example statuses
        return view('admin.disputes.edit', compact('dispute', 'statuses'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Dispute $dispute)
    {
        $request->validate([
            'status' => 'required|string|in:open,under_review,awaiting_client_input,awaiting_freelancer_input,resolved,closed_resolved,closed_unresolved',
            'admin_remarks' => 'nullable|string',
        ]);

        // Capture old values before updating
        $oldStatus = $dispute->status;
        $oldAdminRemarks = $dispute->admin_remarks;

        // Update the dispute
        $dispute->status = $request->status;
        $dispute->admin_remarks = $request->admin_remarks;
        $dispute->save();

        // Check if status or admin remarks have changed
        if ($oldStatus !== $dispute->status || $oldAdminRemarks !== $dispute->admin_remarks) {
            // Create a new DisputeUpdate record
            DisputeUpdate::create([
                'dispute_id' => $dispute->id,
                'user_id' => Auth::id(), // Record the admin who made the update
                'old_status' => $oldStatus,
                'new_status' => $dispute->status,
                'old_admin_remarks' => $oldAdminRemarks,
                'new_admin_remarks' => $dispute->admin_remarks,
            ]);
        }

        // Dispatch event for dispute update by admin
        event(new DisputeUpdatedByAdmin($dispute));

        return redirect()->route('admin.disputes.show', $dispute)->with('success', 'Dispute updated successfully.');
    }

    /**
     * Download the evidence file for a dispute.
     */
    public function downloadEvidence(Dispute $dispute)
    {
        if ($dispute->evidence_path && Storage::disk('private')->exists($dispute->evidence_path)) {
            return Storage::disk('private')->download($dispute->evidence_path);
        }
        return redirect()->back()->with('error', 'Evidence file not found or access denied.');
    }
}
