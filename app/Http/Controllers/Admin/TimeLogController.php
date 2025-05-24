<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FreelancerTimeLog; // Changed from TimeLog
use App\Models\Admin; // Added for reviewedByAdmin relationship
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Events\TimeLogReviewedByAdmin;
use App\Events\ClientNotificationForApprovedTimeLog;

class TimeLogController extends Controller
{
    public function index(Request $request)
    {
        $query = FreelancerTimeLog::with(['freelancer', 'jobAssignment.job', 'reviewedByAdmin']) // Changed relationships
            ->orderBy('created_at', 'desc');

        // Add filtering options
        if ($request->has('freelancer_id') && $request->freelancer_id != '') {
            $query->where('freelancer_id', $request->freelancer_id);
        }

        if ($request->has('job_id') && $request->job_id != '') {
            $query->whereHas('jobAssignment.job', function ($q) use ($request) {
                $q->where('id', $request->job_id);
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->start_date != '') {
            $query->whereDate('start_time', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date != '') {
            $query->whereDate('end_time', '<=', $request->end_date);
        }

        $timeLogs = $query->paginate(15);

        // For filter dropdowns
        $freelancers = \App\Models\User::where('role', \App\Models\User::ROLE_FREELANCER)->get();
        $jobs = \App\Models\Job::all();
        $statuses = ['pending', 'approved', 'rejected']; // Define possible statuses

        return view('admin.time-logs.index', compact('timeLogs', 'freelancers', 'jobs', 'statuses'));
    }

    public function show(FreelancerTimeLog $timeLog) // Changed type hint
    {
        $timeLog->load(['freelancer', 'jobAssignment.job', 'reviewedByAdmin']); // Changed relationships
        return view('admin.time-logs.show', compact('timeLog'));
    }

    public function review(Request $request, FreelancerTimeLog $timeLog) // Changed type hint
    {
        $request->validate([
            'status' => 'required|in:approved,rejected', // Adjusted statuses
            'admin_comments' => 'nullable|string',
        ]);

        if ($timeLog->status !== 'pending') { // Adjusted status check
            return redirect()->route('admin.time-logs.show', $timeLog)->with('error', 'This time log has already been reviewed or is still running.');
        }

        $timeLog->status = $request->input('status');
        $timeLog->notes = $request->input('admin_comments'); // Using notes for admin comments
        $timeLog->reviewed_by_admin_id = auth('admin')->id();
        $timeLog->reviewed_at = now();
        $timeLog->save();

        event(new TimeLogReviewedByAdmin($timeLog)); // Notify freelancer

        if ($timeLog->status === 'approved') { // Adjusted status check
            event(new ClientNotificationForApprovedTimeLog($timeLog)); // Notify client
        }

        return redirect()->route('admin.time-logs.show', $timeLog)->with('success', 'Time log review submitted successfully.');
    }

    public function downloadProof(FreelancerTimeLog $timeLog) // Changed type hint
    {
        // Assuming proof of work is handled differently or not directly in this model for now
        // If proof of work is needed, it should be added to FreelancerTimeLog model and migration
        abort(404, 'Proof of work download not implemented for FreelancerTimeLog.');
    }
}
