<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\JobAssignment; // Import JobAssignment model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Assuming admin user is authenticated
use Illuminate\Support\Facades\DB; // For transactions

class PaymentController extends Controller
{
    /**
     * Display a listing of payments.
     */
    public function index()
    {
        $payments = Payment::with(['jobAssignment.job', 'freelancer'])
                           ->latest()
                           ->get();
        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create()
    {
        // Admins create payments, but typically linked from a completed job assignment
        // This method might not be directly used via a simple /create route
        // We might need to pass job_assignment_id
        $jobAssignments = JobAssignment::where('status', 'completed') // Only create payments for completed assignments
                                       ->with(['job', 'freelancer', 'timeLogs']) // Eager load timeLogs
                                       ->get();

        return view('admin.payments.create', compact('jobAssignments'));
    }

    /**
     * Store a newly created payment in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'job_assignment_id' => ['required', 'exists:job_assignments,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $assignment = JobAssignment::findOrFail($request->job_assignment_id);

        // Ensure the assignment is completed before creating a payment
        if ($assignment->status !== 'completed') {
             return redirect()->back()->with('error', 'Payment can only be created for completed job assignments.');
        }

        // Prevent duplicate payments for the same assignment
        if (Payment::where('job_assignment_id', $assignment->id)->exists()) {
             return redirect()->back()->with('error', 'A payment already exists for this job assignment.');
        }

        DB::beginTransaction();
        try {
            $payment = Payment::create([
                'job_assignment_id' => $assignment->id,
                'freelancer_id' => $assignment->freelancer_id,
                'amount' => $request->amount,
                'status' => 'pending', // Initial status
                'admin_notes' => $request->admin_notes,
            ]);

            // Optionally update assignment status to 'payment_pending' or similar
            // $assignment->status = 'payment_pending';
            // $assignment->save();

            DB::commit();

            // Dispatch event for payment created
            event(new PaymentProcessed($payment->load(['jobAssignment.job', 'freelancer.user']))); // Eager load relationships for the listener

            return redirect()->route('admin.payments.index')->with('success', 'Payment created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error $e->getMessage()
            return redirect()->back()->with('error', 'Failed to create payment. Please try again.');
        }
    }

    /**
     * Display the specified payment.
     */
    public function show(Payment $payment)
    {
        $payment->load(['jobAssignment.job', 'freelancer', 'jobAssignment.timeLogs']); // Eager load timeLogs
        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified payment.
     */
    public function edit(Payment $payment)
    {
        // Admins can edit payment details or status
         $payment->load(['jobAssignment.job', 'freelancer', 'jobAssignment.timeLogs']); // Eager load timeLogs
         return view('admin.payments.edit', compact('payment'));
    }

    /**
     * Update the specified payment in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'status' => ['required', 'in:pending,processing,completed,failed'],
            'transaction_id' => ['nullable', 'string', 'max:255'],
            'admin_notes' => ['nullable', 'string'],
        ]);

        DB::beginTransaction();
        try {
            $payment->update($request->all());

            // Optionally update assignment status based on payment status
            // if ($payment->status === 'completed') {
            //     $payment->jobAssignment->status = 'paid';
            //     $payment->jobAssignment->save();
            // }

            DB::commit();

            // TODO: Dispatch event for payment status updated

            return redirect()->route('admin.payments.show', $payment)->with('success', 'Payment updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error $e->getMessage()
            return redirect()->back()->with('error', 'Failed to update payment. Please try again.');
        }
    }

    /**
     * Remove the specified payment from storage.
     */
    public function destroy(Payment $payment)
    {
        DB::beginTransaction();
        try {
            $payment->delete();
            DB::commit();
            return redirect()->route('admin.payments.index')->with('success', 'Payment deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error $e->getMessage()
            return redirect()->back()->with('error', 'Failed to delete payment. Please try again.');
        }
    }
}
