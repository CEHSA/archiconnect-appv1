<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\JobAssignment; // Changed from AssignmentTask
use App\Models\FreelancerTimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // Added for file operations
use Carbon\Carbon;
use App\Events\FreelancerTimeLogStarted;
use App\Events\FreelancerTimeLogStopped;

class TimeLogController extends Controller
{
    public function index(Request $request)
    {
        $freelancerId = Auth::id();

        // Get active job assignments for the current freelancer
        $activeAssignments = JobAssignment::where('freelancer_id', $freelancerId)
            ->where('status', 'in_progress') // Assuming 'in_progress' is the status for active assignments
            ->with('job')
            ->get();

        // Get any currently running time log for the current freelancer
        $runningTimeLog = FreelancerTimeLog::where('freelancer_id', $freelancerId)
            ->where('status', FreelancerTimeLog::STATUS_RUNNING)
            ->with('jobAssignment.job')
            ->first();

        // Get paginated time logs for the current freelancer
        $timeLogs = FreelancerTimeLog::where('freelancer_id', $freelancerId)
            ->with('jobAssignment.job')
            ->orderBy('start_time', 'desc')
            ->paginate(15);

        return view('freelancer.time-logs.index', compact('timeLogs', 'activeAssignments', 'runningTimeLog'));
    }

    public function startTimer(Request $request, JobAssignment $assignment) // Changed type hint
    {
        // Check if there's an existing running timer for this assignment by this freelancer
        $existingLog = FreelancerTimeLog::where('job_assignment_id', $assignment->id)
            ->where('freelancer_id', Auth::id())
            ->where('status', FreelancerTimeLog::STATUS_RUNNING)
            ->first();

        if ($existingLog) {
            return redirect()->back()->with('error', 'You already have a timer running for this assignment.');
        }

        $timeLog = FreelancerTimeLog::create([
            'job_assignment_id' => $assignment->id,
            'freelancer_id' => Auth::id(),
            'start_time' => Carbon::now(),
            'status' => FreelancerTimeLog::STATUS_RUNNING,
        ]);

        event(new FreelancerTimeLogStarted($timeLog)); // Notify admin

        return redirect()->back()->with('success', 'Timer started successfully.');
    }

    public function stopTimer(Request $request, FreelancerTimeLog $timeLog)
    {
        if ($timeLog->freelancer_id !== Auth::id() || $timeLog->status !== FreelancerTimeLog::STATUS_RUNNING) {
            return redirect()->back()->with('error', 'Invalid action.');
        }

        $request->validate([
            'notes' => 'nullable|string',
            'proof_of_work' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:20480',
        ]);

        $endTime = Carbon::now();
        $durationMinutes = $endTime->diffInMinutes($timeLog->start_time);

        $timeLog->end_time = $endTime;
        $timeLog->duration_minutes = $durationMinutes;
        $timeLog->status = FreelancerTimeLog::STATUS_PENDING_REVIEW;
        $timeLog->notes = $request->input('notes');

        if ($request->hasFile('proof_of_work')) {
            $file = $request->file('proof_of_work');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('proof_of_work/' . $timeLog->job_assignment_id, $filename, 'private');
            $timeLog->proof_of_work_path = $path;
            $timeLog->proof_of_work_filename = $filename;
        }

        $timeLog->save();

        event(new FreelancerTimeLogStopped($timeLog)); // Notify admin

        return redirect()->back()->with('success', 'Timer stopped. Log submitted for review.');
    }

    public function updateLog(Request $request, FreelancerTimeLog $timeLog)
    {
        if ($timeLog->freelancer_id !== Auth::id() || $timeLog->status !== FreelancerTimeLog::STATUS_PENDING_REVIEW) {
             return redirect()->back()->with('error', 'You can only update logs that are pending review.');
        }

        $request->validate([
            'notes' => 'nullable|string',
            'proof_of_work' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:20480',
        ]);

        $timeLog->notes = $request->input('notes');

        if ($request->hasFile('proof_of_work')) {
            // Delete old proof of work if exists
            if ($timeLog->proof_of_work_path) {
                Storage::disk('private')->delete($timeLog->proof_of_work_path);
            }

            $file = $request->file('proof_of_work');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('proof_of_work/' . $timeLog->job_assignment_id, $filename, 'private');
            $timeLog->proof_of_work_path = $path;
            $timeLog->proof_of_work_filename = $filename;
        }
        $timeLog->save();
        return redirect()->back()->with('success', 'Time log updated successfully.');
    }

    public function destroy(FreelancerTimeLog $timeLog)
    {
        if ($timeLog->freelancer_id !== Auth::id() || !in_array($timeLog->status, [FreelancerTimeLog::STATUS_PENDING_REVIEW, FreelancerTimeLog::STATUS_DECLINED])) {
            return redirect()->back()->with('error', 'You can only delete logs that are pending review or rejected.');
        }

        // Delete proof of work file if exists
        if ($timeLog->proof_of_work_path) {
            Storage::disk('private')->delete($timeLog->proof_of_work_path);
        }

        $timeLog->delete();
        return redirect()->back()->with('success', 'Time log deleted successfully.');
    }

    /**
     * Synchronize offline time logs with the backend.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sync(Request $request)
    {
        $request->validate([
            '*.offline_id' => 'required|string',
            '*.job_assignment_id' => 'required|exists:job_assignments,id',
            '*.start_time' => 'required|date',
            '*.end_time' => 'nullable|date|after_or_equal:start_time',
            '*.duration_minutes' => 'required|integer|min:0',
            '*.notes' => 'nullable|string',
            '*.status' => 'required|string', // e.g., 'running', 'pending_sync', 'pending_review'
            '*.is_offline_recorded' => 'required|boolean',
            // '.*.proof_of_work' => 'nullable|string', // If you decide to base64 encode and send files
        ]);

        $syncedLogs = [];
        foreach ($request->json()->all() as $logData) {
            // Ensure the log belongs to the authenticated freelancer
            if ($logData['freelancer_id'] !== Auth::id()) {
                continue; // Skip logs that don't belong to the current user
            }

            // Check if a log with this offline_id already exists for this freelancer
            $existingLog = FreelancerTimeLog::where('freelancer_id', Auth::id())
                                            ->where('offline_id', $logData['offline_id'])
                                            ->first();

            if ($existingLog) {
                // Update existing log (e.g., if it was running offline and now stopped)
                $existingLog->update([
                    'end_time' => $logData['end_time'] ? Carbon::parse($logData['end_time']) : null,
                    'duration_minutes' => $logData['duration_minutes'],
                    'notes' => $logData['notes'],
                    'status' => $logData['status'] === 'running' ? FreelancerTimeLog::STATUS_RUNNING : FreelancerTimeLog::STATUS_PENDING_REVIEW, // Convert 'pending_sync' to 'pending_review'
                    'is_offline_recorded' => true,
                ]);
                $syncedLogs[] = $existingLog;
            } else {
                // Create new log
                $newLog = FreelancerTimeLog::create([
                    'freelancer_id' => Auth::id(),
                    'job_assignment_id' => $logData['job_assignment_id'],
                    'start_time' => Carbon::parse($logData['start_time']),
                    'end_time' => $logData['end_time'] ? Carbon::parse($logData['end_time']) : null,
                    'duration_minutes' => $logData['duration_minutes'],
                    'notes' => $logData['notes'],
                    'status' => $logData['status'] === 'running' ? FreelancerTimeLog::STATUS_RUNNING : FreelancerTimeLog::STATUS_PENDING_REVIEW, // Convert 'pending_sync' to 'pending_review'
                    'is_offline_recorded' => true,
                    'offline_id' => $logData['offline_id'],
                ]);
                $syncedLogs[] = $newLog;
            }
        }

        return response()->json([
            'message' => 'Offline time logs synchronized successfully.',
            'synced_count' => count($syncedLogs),
            'synced_logs' => collect($syncedLogs)->map(fn($log) => ['id' => $log->id, 'offline_id' => $log->offline_id])
        ]);
    }
}
