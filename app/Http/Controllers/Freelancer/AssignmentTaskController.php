<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\AssignmentTask;
use App\Models\JobAssignment; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Added

class AssignmentTaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(JobAssignment $assignment) // Modified
    {
        // TODO: Authorization: Ensure the logged-in freelancer owns this assignment
        // $this->authorize('view', $assignment);

        $tasks = $assignment->tasks()->orderBy('order')->get();
        return view('freelancer.assignments.tasks.index', compact('assignment', 'tasks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(JobAssignment $assignment) // Modified
    {
        // TODO: Authorization
        // $this->authorize('update', $assignment);
        return view('freelancer.assignments.tasks.create', compact('assignment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, JobAssignment $assignment) // Modified
    {
        // TODO: Authorization
        // $this->authorize('update', $assignment);

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

        $assignment->tasks()->create($taskData);

        return redirect()->route('freelancer.assignments.show', $assignment)
            ->with('success', 'Task created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(AssignmentTask $assignmentTask)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssignmentTask $assignmentTask) // $assignmentTask is bound directly
    {
        // Check if the AssignmentTask model was found by route model binding
        if (!$assignmentTask->exists) {
            abort(404, 'Assignment Task not found.');
        }

        // Eager load the job assignment and its job for context in the view
        $assignmentTask->load('jobAssignment.job');
        $assignment = $assignmentTask->jobAssignment;

        // Ensure the assignment and its associated job exist
        if (!$assignment || !$assignment->job) {
            abort(404, 'Assignment or associated job not found for this task.');
        }

        // TODO: Authorization: Ensure the logged-in freelancer owns this task's assignment
        // $this->authorize('update', $assignment);
        // $this->authorize('update', $assignmentTask); // Or a specific task policy

        return view('freelancer.assignments.tasks.edit', compact('assignmentTask', 'assignment'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssignmentTask $assignmentTask)
    {
        // TODO: Authorization: Ensure the logged-in freelancer owns this task's assignment
        // $this->authorize('update', $assignmentTask->jobAssignment);
        // $this->authorize('update', $assignmentTask);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:pending,in_progress,completed,cancelled',
            'due_date' => 'nullable|date',
            'order' => 'nullable|integer',
        ]);

        $assignmentTask->update($validated);

        return redirect()->route('freelancer.assignments.show', $assignmentTask->jobAssignment)
            ->with('success', 'Task updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssignmentTask $assignmentTask)
    {
        // TODO: Authorization: Ensure the logged-in freelancer owns this task's assignment
        // $this->authorize('delete', $assignmentTask->jobAssignment);
        // $this->authorize('delete', $assignmentTask);

        $assignment = $assignmentTask->jobAssignment; // Get parent assignment for redirection
        
        // Store the ID before deleting the task, in case $assignment is null
        // or to prevent issues if the relationship is somehow cleared during deletion.
        $jobAssignmentId = $assignment ? $assignment->id : null;

        $assignmentTask->delete();

        if ($jobAssignmentId) {
            return redirect()->route('freelancer.assignments.show', $jobAssignmentId)
                ->with('success', 'Task deleted successfully.');
        } else {
            // If there was no associated assignment, redirect to a general page like the dashboard.
            return redirect()->route('freelancer.dashboard')
                ->with('warning', 'Task deleted, but could not redirect to its assignment page as it was not properly associated.');
        }
    }
}
