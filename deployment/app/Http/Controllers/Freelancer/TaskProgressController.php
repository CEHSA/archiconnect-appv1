<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\JobAssignment; // Add this line
use Illuminate\Support\Facades\Auth; // Add this line
use Illuminate\Support\Facades\Storage; // Add this line
use App\Models\TaskProgress;

class TaskProgressController extends Controller
{
    /**
     * Show the form for creating a new task progress update.
     */
    public function create(JobAssignment $assignment)
    {
        // Ensure the authenticated freelancer is assigned to this job
        if ($assignment->freelancer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('freelancer.assignments.task-progress.create', compact('assignment'));
    }

    /**
     * Store a newly created task progress update.
     */
    public function store(Request $request, JobAssignment $assignment)
    {
        // Ensure the authenticated freelancer is assigned to this job
        if ($assignment->freelancer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Validate the request data
        $validated = $request->validate([
            'description' => ['required', 'string'],
            'progress_file' => ['nullable', 'file', 'max:10240'], // Max 10MB
        ]);

        $filePath = null;
        if ($request->hasFile('progress_file')) {
            $filePath = $request->file('progress_file')->store('task_progress_files', 'private');
        }

        // Create the task progress record
        TaskProgress::create([
            'job_assignment_id' => $assignment->id,
            'freelancer_id' => Auth::id(),
            'description' => $validated['description'],
            'file_path' => $filePath,
            'submitted_at' => now(),
        ]);

        // TODO: Dispatch event to notify admin of new task progress update

        return redirect()->route('freelancer.assignments.show', $assignment)
                         ->with('success', 'Task progress update submitted successfully.');
    }

    /**
     * Handle file download for a task progress update.
     */
    public function download(TaskProgress $taskProgress)
    {
        // Ensure the authenticated freelancer is associated with this task progress
        if ($taskProgress->freelancer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$taskProgress->file_path) {
            abort(404, 'File not found for this task progress update.');
        }

        // Ensure the file exists on the private disk
        if (!Storage::disk('private')->exists($taskProgress->file_path)) {
            abort(404, 'File not found in storage.');
        }

        // Use a generic filename or try to get the original if stored
        $filename = $taskProgress->original_filename ?? 'task_progress_file';

        return Storage::disk('private')->download($taskProgress->file_path, $filename);
    }
}
