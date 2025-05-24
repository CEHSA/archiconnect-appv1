<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\JobAssignment; // Changed from AssignmentTask
use App\Models\FreelancerTimeLog; // Changed from TimeLog
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Events\FreelancerTimeLogStarted;
use App\Events\FreelancerTimeLogStopped;

class TimeLogController extends Controller
{
    public function index(Request $request)
    {
        $timeLogs = FreelancerTimeLog::where('freelancer_id', Auth::id())
            ->with('jobAssignment.job') // Changed relationship
            ->orderBy('start_time', 'desc')
            ->paginate(15);

        return view('freelancer.time-logs.index', compact('timeLogs'));
    }

    public function startTimer(Request $request, JobAssignment $assignment) // Changed type hint
    {
        // Check if there's an existing running timer for this assignment by this freelancer
        $existingLog = FreelancerTimeLog::where('job_assignment_id', $assignment->id) // Changed field
            ->where('freelancer_id', Auth::id())
            ->where('status', 'running') // Adjusted status
            ->first();

        if ($existingLog) {
            return redirect()->back()->with('error', 'You already have a timer running for this assignment.');
        }

        $timeLog = FreelancerTimeLog::create([
            'job_assignment_id' => $assignment->id, // Changed field
            'freelancer_id' => Auth::id(),
            'start_time' => Carbon::now(),
            'status' => 'running', // Adjusted status
        ]);

        event(new FreelancerTimeLogStarted($timeLog)); // Notify admin

        return redirect()->back()->with('success', 'Timer started successfully.');
    }

    public function stopTimer(Request $request, FreelancerTimeLog $timeLog) // Changed type hint
    {
        if ($timeLog->freelancer_id !== Auth::id() || $timeLog->status !== 'running') { // Adjusted status
            return redirect()->back()->with('error', 'Invalid action.');
        }

        $request->validate([
            'notes' => 'nullable|string', // Changed from freelancer_comments
            // 'proof_of_work' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:20480', // Removed for now
        ]);

        $endTime = Carbon::now();
        $durationMinutes = $endTime->diffInMinutes($timeLog->start_time); // Changed to minutes

        $timeLog->end_time = $endTime;
        $timeLog->duration_minutes = $durationMinutes; // Changed field
        $timeLog->status = 'pending'; // Adjusted status
        $timeLog->notes = $request->input('notes'); // Changed field

        // if ($request->hasFile('proof_of_work')) {
        //     $file = $request->file('proof_of_work');
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     $path = $file->storeAs('proof_of_work/' . $timeLog->job_assignment_id, $filename, 'private'); // Adjusted path
        //     $timeLog->proof_of_work_path = $path;
        //     $timeLog->proof_of_work_filename = $filename;
        // }

        $timeLog->save();

        event(new FreelancerTimeLogStopped($timeLog)); // Notify admin

        return redirect()->back()->with('success', 'Timer stopped. Log submitted for review.');
    }

    public function updateLog(Request $request, FreelancerTimeLog $timeLog) // Changed type hint
    {
        if ($timeLog->freelancer_id !== Auth::id() || $timeLog->status !== 'pending') { // Adjusted status
             return redirect()->back()->with('error', 'You can only update logs that are pending review.');
        }

        $request->validate([
            'notes' => 'nullable|string', // Changed from freelancer_comments
            // 'proof_of_work' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:20480', // Removed for now
        ]);

        $timeLog->notes = $request->input('notes'); // Changed field

        // if ($request->hasFile('proof_of_work')) {
        //     $file = $request->file('proof_of_work');
        //     $filename = time() . '_' . $file->getClientOriginalName();
        //     $path = $file->storeAs('proof_of_work/' . $timeLog->job_assignment_id, $filename, 'private'); // Adjusted path
        //     $timeLog->proof_of_work_path = $path;
        //     $timeLog->proof_of_work_filename = $filename;
        // }
        $timeLog->save();
        return redirect()->back()->with('success', 'Time log updated successfully.');
    }

    public function destroy(FreelancerTimeLog $timeLog) // Changed type hint
    {
        if ($timeLog->freelancer_id !== Auth::id() || !in_array($timeLog->status, ['pending', 'rejected'])) { // Adjusted statuses
            return redirect()->back()->with('error', 'You can only delete logs that are pending review or rejected.');
        }

        // Optionally, delete proof of work file
        // if ($timeLog->proof_of_work_path) {
        //     Storage::disk('private')->delete($timeLog->proof_of_work_path);
        // }

        $timeLog->delete();
        return redirect()->back()->with('success', 'Time log deleted successfully.');
    }
}
