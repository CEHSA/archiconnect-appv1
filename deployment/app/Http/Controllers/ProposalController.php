<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Proposal;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProposalController extends Controller
{
    /**
     * Display a listing of the freelancer's proposals.
     */
    public function index(): View
    {
        $proposals = auth()->user()->proposals()->with('job')->latest()->get();
        return view('freelancer.proposals.index', compact('proposals'));
    }

    /**
     * Display a listing of proposals for a specific job (client view).
     */
    public function jobProposals(Job $job): View
    {
        $this->authorize('view-proposals', $job);
        $proposals = $job->proposals()->with('user.freelancerProfile')->latest()->get();
        return view('client.jobs.proposals', compact('job', 'proposals'));
    }

    /**
     * Store a newly created proposal.
     */
    public function store(Request $request, Job $job): RedirectResponse
    {
        $validated = $request->validate([
            'bid_amount' => ['required', 'numeric', 'min:1'],
            'proposal_text' => ['required', 'string', 'min:50'],
        ]);

        $proposal = $job->proposals()->create([
            'user_id' => auth()->id(),
            'bid_amount' => $validated['bid_amount'],
            'proposal_text' => $validated['proposal_text'],
            'status' => 'pending'
        ]);

        return redirect()
            ->route('freelancer.proposals.show', $proposal)
            ->with('success', 'Proposal submitted successfully.');
    }

    /**
     * Show the proposal details.
     */
    public function show(Proposal $proposal): View
    {
        $this->authorize('view', $proposal);
        $proposal->load(['job', 'user.freelancerProfile']);
        return view('freelancer.proposals.show', compact('proposal'));
    }

    /**
     * Update the proposal status (accept/reject).
     */
    public function updateStatus(Request $request, Proposal $proposal): RedirectResponse
    {
        $this->authorize('update-status', $proposal);
        
        $validated = $request->validate([
            'status' => ['required', 'in:accepted,rejected'],
        ]);

        $proposal->update(['status' => $validated['status']]);

        if ($validated['status'] === 'accepted') {
            // Reject all other proposals for this job
            $proposal->job->proposals()
                ->where('id', '!=', $proposal->id)
                ->update(['status' => 'rejected']);
        }

        return redirect()
            ->route('client.jobs.proposals', $proposal->job)
            ->with('success', 'Proposal ' . $validated['status'] . ' successfully.');
    }
}
