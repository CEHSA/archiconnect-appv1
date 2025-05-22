<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\BudgetAppeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\BudgetAppealDecisionMade; // Assuming you will create this event

class BudgetAppealController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Clients don't have a list view for appeals, they review via notification/link
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Clients don't create budget appeals, freelancers do.
        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Clients don't store budget appeals, freelancers do.
        abort(404);
    }

    /**
     * Display the specified budget appeal for client review.
     */
    public function show(BudgetAppeal $budgetAppeal)
    {
        // Ensure the logged-in client is associated with this job's client
        if ($budgetAppeal->jobAssignment->job->user_id !== Auth::id()) {
            abort(403, 'You are not authorized to view this budget appeal.');
        }

        // Ensure the appeal is in a state for client review
        if ($budgetAppeal->status !== 'under_review_by_client') {
             return redirect()->route('client.dashboard')->with('error', 'This budget appeal is not currently under your review.');
        }

        $budgetAppeal->load(['freelancer', 'jobAssignment.job']);
        return view('client.budget-appeals.show', compact('budgetAppeal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BudgetAppeal $budgetAppeal)
    {
        // Clients don't edit budget appeals directly via a form, they update decision/remarks via show view.
        abort(404);
    }

    /**
     * Update the specified budget appeal with the client's decision.
     */
    public function update(Request $request, BudgetAppeal $budgetAppeal)
    {
        // Ensure the logged-in client is associated with this job's client
        if ($budgetAppeal->jobAssignment->job->user_id !== Auth::id()) {
            abort(403, 'You are not authorized to update this budget appeal.');
        }

         // Ensure the appeal is in a state for client review
        if ($budgetAppeal->status !== 'under_review_by_client') {
             return redirect()->route('client.dashboard')->with('error', 'This budget appeal is not currently under your review.');
        }

        $request->validate([
            'client_decision' => ['required', 'in:approved,rejected'],
            'client_remarks' => ['nullable', 'string'],
        ]);

        $budgetAppeal->client_decision = $request->client_decision;
        $budgetAppeal->client_remarks = $request->client_remarks;
        $budgetAppeal->status = $request->client_decision; // Update main status based on client decision
        $budgetAppeal->save();

        // If approved, update the job's not_to_exceed_budget
        if ($budgetAppeal->status === 'approved') {
            $budgetAppeal->jobAssignment->job->update(['not_to_exceed_budget' => $budgetAppeal->requested_budget]);
        }

        // Dispatch event to notify freelancer
        event(new BudgetAppealDecisionMade($budgetAppeal));

        return redirect()->route('client.dashboard')->with('success', 'Budget appeal decision recorded successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BudgetAppeal $budgetAppeal)
    {
        // Clients don't delete budget appeals.
        abort(403);
    }
}
