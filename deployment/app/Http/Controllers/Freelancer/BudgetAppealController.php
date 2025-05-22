<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\JobAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\BudgetAppeal; // Import the BudgetAppeal model
use App\Events\BudgetAppealCreated; // Assuming you will create this event

class BudgetAppealController extends Controller
{
    /**
     * Show the form for creating a new budget appeal.
     */
    public function create(JobAssignment $assignment)
    {
        // Ensure the freelancer is assigned to this job and the assignment is accepted or in progress
        if ($assignment->freelancer_id !== Auth::id() || !in_array($assignment->status, ['accepted', 'in_progress', 'revision_requested'])) {
            abort(403, 'You are not authorized to submit a budget appeal for this assignment.');
        }

        // Check if a pending appeal already exists for this assignment
        $existingAppeal = BudgetAppeal::where('job_assignment_id', $assignment->id)
                                      ->where('status', 'pending')
                                      ->first();

        if ($existingAppeal) {
            return redirect()->route('freelancer.assignments.show', $assignment)->with('error', 'You already have a pending budget appeal for this assignment.');
        }

        return view('freelancer.assignments.budget-appeals.create', compact('assignment'));
    }

    /**
     * Store a newly created budget appeal in storage.
     */
    public function store(Request $request, JobAssignment $assignment)
    {
        // Ensure the freelancer is assigned to this job and the assignment is accepted or in progress
         if ($assignment->freelancer_id !== Auth::id() || !in_array($assignment->status, ['accepted', 'in_progress', 'revision_requested'])) {
            abort(403, 'You are not authorized to submit a budget appeal for this assignment.');
        }

        // Check if a pending appeal already exists for this assignment
        $existingAppeal = BudgetAppeal::where('job_assignment_id', $assignment->id)
                                      ->where('status', 'pending')
                                      ->first();

        if ($existingAppeal) {
            return redirect()->route('freelancer.assignments.show', $assignment)->with('error', 'You already have a pending budget appeal for this assignment.');
        }

        $request->validate([
            'requested_budget' => ['required', 'numeric', 'gt:' . $assignment->job->not_to_exceed_budget],
            'reason' => ['required', 'string'],
            'evidence' => ['nullable', 'file', 'max:10240'], // Max 10MB
        ]);

        $evidencePath = null;
        if ($request->hasFile('evidence')) {
            $evidencePath = $request->file('evidence')->store('budget_appeal_evidence', 'private');
        }

        $appeal = BudgetAppeal::create([
            'job_assignment_id' => $assignment->id,
            'freelancer_id' => Auth::id(),
            'current_budget' => $assignment->job->not_to_exceed_budget,
            'requested_budget' => $request->requested_budget,
            'reason' => $request->reason,
            'evidence_path' => $evidencePath,
            'status' => 'pending', // Default status
        ]);

        // Dispatch event to notify admins
        event(new BudgetAppealCreated($appeal));

        return redirect()->route('freelancer.assignments.show', $assignment)->with('success', 'Budget appeal submitted successfully.');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BudgetAppeal $budgetAppeal)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BudgetAppeal $budgetAppeal)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BudgetAppeal $budgetAppeal)
    {
        //
    }
}
