<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BudgetAppeal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str; // Import Str facade

class BudgetAppealController extends Controller
{
    /**
     * Display a listing of budget appeals.
     */
    public function index()
    {
        $budgetAppeals = BudgetAppeal::with(['freelancer', 'jobAssignment.job'])->latest()->get();
        return view('admin.budget-appeals.index', compact('budgetAppeals'));
    }

    /**
     * Show the form for creating a new budget appeal.
     */
    public function create()
    {
        // Admins don't create budget appeals, freelancers do.
        abort(404);
    }

    /**
     * Store a newly created budget appeal in storage.
     */
    public function store(Request $request)
    {
        // Admins don't store budget appeals, freelancers do.
        abort(404);
    }

    /**
     * Display the specified budget appeal.
     */
    public function show(BudgetAppeal $budgetAppeal)
    {
        $budgetAppeal->load(['freelancer', 'jobAssignment.job']);
        return view('admin.budget-appeals.show', compact('budgetAppeal'));
    }

    /**
     * Show the form for editing the specified budget appeal.
     */
    public function edit(BudgetAppeal $budgetAppeal)
    {
         // Admins don't edit budget appeals directly via a form, they update status/remarks via show view.
        abort(404);
    }

    /**
     * Update the specified budget appeal in storage.
     */
    public function update(Request $request, BudgetAppeal $budgetAppeal)
    {
        $request->validate([
            'status' => ['required', 'in:under_review_by_client,rejected'], // Admin can only forward or reject
            'admin_remarks' => ['nullable', 'string'],
        ]);

        // Ensure the appeal is pending before admin can act
        if ($budgetAppeal->status !== 'pending') {
             return redirect()->route('admin.budget-appeals.show', $budgetAppeal)->with('error', 'Budget appeal is not in a pending state.');
        }

        $budgetAppeal->status = $request->status;
        $budgetAppeal->admin_remarks = $request->admin_remarks;
        $budgetAppeal->save();

        // Dispatch event to notify client
        event(new \App\Events\BudgetAppealForwardedToClient($budgetAppeal));

        return redirect()->route('admin.budget-appeals.show', $budgetAppeal)->with('success', 'Budget appeal forwarded to client successfully.');
    }

    /**
     * Remove the specified budget appeal from storage.
     */
    public function destroy(BudgetAppeal $budgetAppeal)
    {
        // Admins can delete appeals if necessary
         $budgetAppeal->delete();
         return redirect()->route('admin.budget-appeals.index')->with('success', 'Budget appeal deleted successfully.');
    }

    /**
     * Download the evidence file for the specified budget appeal.
     */
    public function downloadEvidence(BudgetAppeal $budgetAppeal)
    {
        if (! $budgetAppeal->evidence_path || ! Storage::disk('private')->exists($budgetAppeal->evidence_path)) {
            abort(404, 'Evidence file not found.');
        }

        // Generate a user-friendly filename
        $filename = 'budget_appeal_' . $budgetAppeal->id . '_evidence.' . pathinfo($budgetAppeal->evidence_path, PATHINFO_EXTENSION);

        return Storage::disk('private')->download($budgetAppeal->evidence_path, $filename);
    }
}
