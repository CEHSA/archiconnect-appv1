<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AssignmentTask;
use App\Models\JobAssignment; // Added
use Illuminate\Http\Request;

class AssignmentTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(JobAssignment $jobAssignment) // Renamed parameter for clarity
    {
        $jobAssignment->load('job'); // Load job for context
        $tasks = $jobAssignment->tasks()->orderBy('order')->get();
        return view('admin.assignments.tasks.index', compact('jobAssignment', 'tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(JobAssignment $jobAssignment) // Renamed parameter
    {
        $jobAssignment->load('job');
        return view('admin.assignments.tasks.create', compact('jobAssignment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, JobAssignment $jobAssignment) // Renamed parameter
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'nullable|string|in:pending,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
            'order' => 'nullable|integer',
        ]);

        $taskData = $validated;
        $taskData['status'] = $validated['status'] ?? 'pending';
        $taskData['order'] = $validated['order'] ?? 0;

        $jobAssignment->tasks()->create($taskData);

        // Redirect to admin job assignment show page, or task index for that assignment
        return redirect()->route('admin.job-assignments.show', $jobAssignment) 
                         ->with('success', 'Task created successfully for assignment.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AssignmentTask $assignmentTask)
    {
        // Typically, for admin, tasks might be shown as part of job assignment view or a dedicated task list.
        // If a separate show view is needed, implement here. For now, redirect to edit or list.
        $assignmentTask->load('jobAssignment.job');
        $jobAssignment = $assignmentTask->jobAssignment;
        // return view('admin.assignments.tasks.show', compact('assignmentTask', 'jobAssignment'));
        return redirect()->route('admin.tasks.edit', $assignmentTask);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssignmentTask $assignmentTask)
    {
        $assignmentTask->load('jobAssignment.job');
        $jobAssignment = $assignmentTask->jobAssignment;
        return view('admin.assignments.tasks.edit', compact('assignmentTask', 'jobAssignment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssignmentTask $assignmentTask)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:pending,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
            'order' => 'nullable|integer',
        ]);

        $assignmentTask->update($validated);
        $jobAssignment = $assignmentTask->jobAssignment;

        return redirect()->route('admin.job-assignments.show', $jobAssignment)
                         ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssignmentTask $assignmentTask)
    {
        $jobAssignment = $assignmentTask->jobAssignment; // Get parent for redirection
        $assignmentTask->delete();

        return redirect()->route('admin.job-assignments.tasks.index', $jobAssignment)
                         ->with('success', 'Task deleted successfully.');
    }
}
