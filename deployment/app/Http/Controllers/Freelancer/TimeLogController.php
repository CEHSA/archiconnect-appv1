<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\AssignmentTask;
use App\Models\TimeLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Events\FreelancerTimeLogStarted; // Will create this event
use App\Events\FreelancerTimeLogStopped; // Will create this event

class TimeLogController extends Controller
{
    public function index(Request $request)
    {
        $timeLogs = TimeLog::where('freelancer_id', Auth::id())
            ->with('assignmentTask.jobAssignment.job') // Eager load related data
            ->orderBy('start_time', 'desc')
            ->paginate(15); // Or any other number

        return view('freelancer.time-logs.index', compact('timeLogs'));
    }

    public function startTimer(Request $request, AssignmentTask $task)
    {
        // Check if there's an existing running timer for this task by this freelancer
        $existingLog = TimeLog::where('assignment_task_id', $task->id)
            ->where('freelancer_id', Auth::id())
            ->where('status', TimeLog::STATUS_RUNNING)
            ->first();

        if ($existingLog) {
            return redirect()->back()->with('error', 'You already have a timer running for this task.');
        }

        $timeLog = TimeLog::create([
            'assignment_task_id' => $task->id,
            'freelancer_id' => Auth::id(),
            'start_time' => Carbon::now(),
            'status' => TimeLog::STATUS_RUNNING,
        ]);

        event(new FreelancerTimeLogStarted($timeLog)); // Notify admin

        return redirect()->back()->with('success', 'Timer started successfully.');
    }

    public function stopTimer(Request $request, TimeLog $timeLog)
    {
        if ($timeLog->freelancer_id !== Auth::id() || $timeLog->status !== TimeLog::STATUS_RUNNING) {
            return redirect()->back()->with('error', 'Invalid action.');
        }

        $request->validate([
            'freelancer_comments' => 'nullable|string',
            'proof_of_work' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:20480', // Max 20MB
        ]);

        $endTime = Carbon::now();
        $durationSeconds = $endTime->diffInSeconds($timeLog->start_time);

        $timeLog->end_time = $endTime;
        $timeLog->duration_seconds = $durationSeconds;
        $timeLog->status = TimeLog::STATUS_PENDING_REVIEW;
        $timeLog->freelancer_comments = $request->input('freelancer_comments');

        if ($request->hasFile('proof_of_work')) {
            $file = $request->file('proof_of_work');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('proof_of_work/' . $timeLog->assignment_task_id, $filename, 'private');
            $timeLog->proof_of_work_path = $path;
            $timeLog->proof_of_work_filename = $filename;
        }

        $timeLog->save();

        event(new FreelancerTimeLogStopped($timeLog)); // Notify admin

        return redirect()->back()->with('success', 'Timer stopped. Log submitted for review.');
    }

    public function updateLog(Request $request, TimeLog $timeLog)
    {
        if ($timeLog->freelancer_id !== Auth::id() || $timeLog->status !== TimeLog::STATUS_PENDING_REVIEW) {
             return redirect()->back()->with('error', 'You can only update logs that are pending review.');
        }

        $request->validate([
            'freelancer_comments' => 'nullable|string',
            'proof_of_work' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:20480',
        ]);

        $timeLog->freelancer_comments = $request->input('freelancer_comments');

        if ($request->hasFile('proof_of_work')) {
            // Optionally, delete old proof if it exists
            // Storage::disk('private')->delete($timeLog->proof_of_work_path);

            $file = $request->file('proof_of_work');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('proof_of_work/' . $timeLog->assignment_task_id, $filename, 'private');
            $timeLog->proof_of_work_path = $path;
            $timeLog->proof_of_work_filename = $filename;
        }
        $timeLog->save();
        return redirect()->back()->with('success', 'Time log updated successfully.');
    }

    public function destroy(TimeLog $timeLog)
    {
        if ($timeLog->freelancer_id !== Auth::id() || !in_array($timeLog->status, [TimeLog::STATUS_PENDING_REVIEW, TimeLog::STATUS_DECLINED])) {
            return redirect()->back()->with('error', 'You can only delete logs that are pending review or declined.');
        }

        // Optionally, delete proof of work file
        // if ($timeLog->proof_of_work_path) {
        //     Storage::disk('private')->delete($timeLog->proof_of_work_path);
        // }

        $timeLog->delete();
        return redirect()->back()->with('success', 'Time log deleted successfully.');
    }
}
