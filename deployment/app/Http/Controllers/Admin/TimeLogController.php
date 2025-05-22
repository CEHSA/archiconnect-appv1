<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TimeLog;
use App\Models\AssignmentTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Events\TimeLogReviewedByAdmin;
use App\Events\ClientNotificationForApprovedTimeLog;

class TimeLogController extends Controller
{
    public function index(Request $request)
    {
        // Add filtering options later (e.g., by freelancer, job, status)
        $timeLogs = TimeLog::with(['freelancer', 'assignmentTask.jobAssignment.job'])
            ->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.time-logs.index', compact('timeLogs'));
    }

    public function show(TimeLog $timeLog)
    {
        $timeLog->load(['freelancer', 'assignmentTask.jobAssignment.job', 'reviewedByAdmin']);
        return view('admin.time-logs.show', compact('timeLog'));
    }

    public function review(Request $request, TimeLog $timeLog)
    {
        $request->validate([
            'status' => 'required|in:' . TimeLog::STATUS_APPROVED . ',' . TimeLog::STATUS_DECLINED,
            'admin_comments' => 'nullable|string',
        ]);

        if ($timeLog->status !== TimeLog::STATUS_PENDING_REVIEW) {
            return redirect()->route('admin.time-logs.show', $timeLog)->with('error', 'This time log has already been reviewed or is still running.');
        }

        $timeLog->status = $request->input('status');
        $timeLog->admin_comments = $request->input('admin_comments');
        $timeLog->reviewed_by_admin_id = auth('admin')->id();
        $timeLog->reviewed_at = now();
        $timeLog->save();

        event(new TimeLogReviewedByAdmin($timeLog)); // Notify freelancer

        if ($timeLog->status === TimeLog::STATUS_APPROVED) {
            event(new ClientNotificationForApprovedTimeLog($timeLog)); // Notify client
        }

        return redirect()->route('admin.time-logs.show', $timeLog)->with('success', 'Time log review submitted successfully.');
    }

    public function downloadProof(TimeLog $timeLog)
    {
        if (!$timeLog->proof_of_work_path) {
            abort(404, 'Proof of work not found.');
        }

        // Ensure admin has permission to download, or if it's the freelancer who owns it.
        // This basic check assumes any authenticated admin can download.
        // Add more specific authorization if needed.

        return Storage::disk('private')->download($timeLog->proof_of_work_path, $timeLog->proof_of_work_filename);
    }
}
