<?php

namespace App\Http\Controllers;

use App\Models\Dispute;
use App\Models\JobAssignment;
use App\Events\DisputeCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DisputeController extends Controller
{
    /**
     * Show the form for creating a new dispute.
     * Users can only report disputes for job assignments they are part of.
     */
    public function create(JobAssignment $jobAssignment)
    {
        // Authorize: Ensure the authenticated user is either the client of the job or the assigned freelancer
        $user = Auth::user();
        $job = $jobAssignment->job;

        if (!($user->id === $job->client_id || $user->id === $jobAssignment->freelancer_id)) {
            abort(403, 'Unauthorized action. You are not part of this job assignment.');
        }

        // Prevent duplicate open disputes for the same assignment by the same reporter
        $existingDispute = Dispute::where('job_assignment_id', $jobAssignment->id)
                                ->where('reporter_id', $user->id)
                                ->whereNotIn('status', ['resolved', 'closed_resolved', 'closed_unresolved'])
                                ->first();

        if ($existingDispute) {
            return redirect()->back()->with('error', 'You already have an open dispute for this job assignment.');
        }

        return view('disputes.create', compact('jobAssignment'));
    }

    /**
     * Store a newly created dispute in storage.
     */
    public function store(Request $request, JobAssignment $jobAssignment)
    {
        $user = Auth::user();
        $job = $jobAssignment->job;

        // Authorization check (same as create)
        if (!($user->id === $job->client_id || $user->id === $jobAssignment->freelancer_id)) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'reason' => 'required|string|max:2000',
            'evidence' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:5120', // Max 5MB
        ]);

        // Determine the reported party
        $reportedId = ($user->id === $job->client_id) ? $jobAssignment->freelancer_id : $job->client_id;

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('dispute_evidence', 'private');
        }

        $newDispute = Dispute::create([
            'job_assignment_id' => $jobAssignment->id,
            'reporter_id' => $user->id,
            'reported_id' => $reportedId,
            'reason' => $request->reason,
            'evidence_path' => $evidencePath,
            'status' => 'open', // Default status
        ]);

        // Dispatch event for new dispute
        event(new DisputeCreated($newDispute));

        // Redirect based on role or to a generic 'disputes submitted' page
        $redirectRoute = 'dashboard'; // Fallback
        if ($user->hasRole('client')) {
            $redirectRoute = 'client.dashboard'; // Assuming client dashboard route
        } elseif ($user->hasRole('freelancer')) {
            $redirectRoute = 'freelancer.dashboard'; // Assuming freelancer dashboard route
        }
        
        // A more specific redirect might be to the job assignment page or a list of their disputes
        // For now, redirecting to a generic dashboard with a success message.
        return redirect()->route($redirectRoute)->with('success', 'Dispute submitted successfully. An admin will review it shortly.');
    }

    /**
     * Display a listing of disputes for the authenticated freelancer.
     */
    public function freelancerIndex()
    {
        $user = Auth::user();
        $disputes = Dispute::where('reporter_id', $user->id)
                            ->orWhere('reported_id', $user->id)
                            ->with(['jobAssignment.job', 'reporter', 'reportedUser']) // Corrected relationship name
                            ->latest()
                            ->paginate(10);

        return view('freelancer.disputes.index', compact('disputes'));
    }

    /**
     * Display a listing of disputes for the authenticated client.
     */
    public function clientIndex()
    {
        $user = Auth::user();
        $disputes = Dispute::where('reporter_id', $user->id)
                            ->orWhere('reported_id', $user->id)
                            ->with(['jobAssignment.job', 'reporter', 'reportedUser'])
                            ->latest()
                            ->paginate(10);

        // TODO: Create client.disputes.index view
        return view('client.disputes.index', compact('disputes'));
    }

    /**
     * Display the specified dispute.
     * Users can only view disputes they are involved in.
     */
    public function show(Dispute $dispute)
    {
        $user = Auth::user();

        // Authorize: Ensure the authenticated user is either the reporter or the reported party
        if (!($user->id === $dispute->reporter_id || $user->id === $dispute->reported_id)) {
            abort(403, 'Unauthorized action. You are not involved in this dispute.');
        }

        $dispute->load(['jobAssignment.job', 'reporter', 'reportedUser', 'updates.user']); // Eager load updates and the user who made the update

        // TODO: Create disputes.show view (generic for client/freelancer, or role-specific views)
        // For now, assuming a generic view path, adjust if role-specific views are created.
        if ($user->hasRole('client')) {
            return view('client.disputes.show', compact('dispute'));
        } elseif ($user->hasRole('freelancer')) {
            return view('freelancer.disputes.show', compact('dispute'));
        }
        // Fallback or error if user role is not client/freelancer, though middleware should prevent this.
        abort(403, 'Unauthorized role for viewing dispute.');
    }
}
