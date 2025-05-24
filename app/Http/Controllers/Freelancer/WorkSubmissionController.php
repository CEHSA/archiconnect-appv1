<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\WorkSubmission;
use App\Models\JobAssignment;
use App\Events\FreelancerWorkSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WorkSubmissionController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create(JobAssignment $assignment)
    {
        // Ensure the freelancer owns this assignment and it's in a state that allows submissions
        if ($assignment->freelancer_id !== Auth::id() || !in_array($assignment->status, ['accepted', 'in_progress', 'revision_requested', 'assigned'])) {
            abort(403, 'Unauthorized action or assignment not in a submittable state.');
        }
        return view('freelancer.assignments.submissions.create', compact('assignment'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, JobAssignment $assignment)
    {
        // Ensure the freelancer owns this assignment and it's in a state that allows submissions
        if ($assignment->freelancer_id !== Auth::id() || !in_array($assignment->status, ['accepted', 'in_progress', 'revision_requested', 'assigned'])) {
            abort(403, 'Unauthorized action or assignment not in a submittable state.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'submission_file' => 'required|file|mimes:pdf,doc,docx,zip,jpg,jpeg,png|max:20480', // Max 20MB
        ]);

        $filePath = null;
        $originalFilename = null;
        $mimeType = null;
        $fileSize = null;

        if ($request->hasFile('submission_file')) {
            $file = $request->file('submission_file');
            $originalFilename = $file->getClientOriginalName();
            $filePath = $file->store("job_assignments/{$assignment->id}/submissions", 'private'); // Store in 'private' disk
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();
        }

        $workSubmission = WorkSubmission::create([
            'job_assignment_id' => $assignment->id,
            'freelancer_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'file_path' => $filePath,
            'original_filename' => $originalFilename,
            'mime_type' => $mimeType,
            'size' => $fileSize,
            'status' => WorkSubmission::STATUS_SUBMITTED_FOR_ADMIN_REVIEW, // Updated status
            'submitted_at' => now(),
        ]);

        // Optionally, update job assignment status if this is the first submission
        if ($assignment->status === 'accepted') {
            $assignment->status = 'in_progress'; // Or 'submitted_for_review'
            $assignment->save();
        }

        // Update the parent Job status
        $job = $assignment->job; // Assumes 'job' relationship exists on JobAssignment
        if ($job) {
            $job->status = 'work_submitted'; // Consider using a Job model constant if available
            $job->save();
        }

        // Notify Admin of new submission
        event(new FreelancerWorkSubmitted($workSubmission));

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Work submitted successfully. Admin has been notified.',
                'data' => $workSubmission
            ], 201);
        }

        return redirect()->route('freelancer.assignments.show', $assignment->id)
                         ->with('success', 'Work submitted successfully. Admin has been notified.');
    }

    // Other resource methods (index, show, edit, update, destroy) can be added later if needed.
    // For now, freelancers primarily create submissions. Viewing might be part of the assignment show page.
}
