<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Job; // Added for Job context
use App\Models\JobAssignment;
use App\Models\Message;
use App\Models\Admin; // Added for Admin context
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\FreelancerMessageCreated; 
use Illuminate\View\View; // Added for type hinting
use Illuminate\Http\RedirectResponse; // Added for type hinting

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all conversations where the freelancer is a participant
        $conversations = Conversation::forUser(Auth::user())
            ->with(['job', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->latest('last_message_at')
            ->get();

        return view('freelancer.messages.index', compact('conversations'));
    }

    /**
     * Display the specified conversation with its messages.
     */
    public function show(Conversation $conversation)
    {
        // Ensure the authenticated freelancer is part of the conversation
        if ($conversation->participant1_id !== Auth::id() && $conversation->participant2_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Load the conversation with its messages and participants
        $conversation->load(['messages.user', 'messages.attachments', 'participant1', 'participant2', 'job']);

        // Mark unread messages as read
        $conversation->messages()
            ->whereNull('read_at')
            ->where('user_id', '!=', Auth::id())
            ->update(['read_at' => now()]);

        return view('freelancer.messages.show', compact('conversation'));
    }

    /**
     * Show the form for creating a new message.
     */
    public function create(JobAssignment $assignment)
    {
        // Ensure the authenticated freelancer is assigned to this job
        if ($assignment->freelancer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // TODO: Find or create a conversation for this assignment and the admin.
        // For now, we'll just pass the assignment to the view.
        // The form will need to handle finding/creating the conversation before storing the message.

        return view('freelancer.assignments.messages.create', compact('assignment'));
    }

    /**
     * Store a newly created message in storage.
     */
    public function createAdminMessage()
    {
        $freelancer = Auth::user();
        $assignments = JobAssignment::where('freelancer_id', $freelancer->id)
            ->with(['job', 'tasks' => function ($query) {
                $query->orderBy('title');
            }])
            ->whereIn('status', ['accepted', 'in_progress', 'revision_requested']) // Only active/relevant assignments
            ->get();

        // Prepare a structured list for the dropdowns
        $assignmentOptions = [];
        foreach ($assignments as $assignment) {
            $tasks = [];
            foreach ($assignment->tasks as $task) {
                $tasks[] = [
                    'id' => $task->id,
                    'title' => $task->title,
                ];
            }
            $assignmentOptions[] = [
                'id' => $assignment->id,
                'job_title' => $assignment->job->title,
                'tasks' => $tasks,
            ];
        }

        return view('freelancer.messages.create-admin', compact('assignmentOptions'));
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'conversation_id' => 'nullable|exists:conversations,id',
            'job_assignment_id' => 'nullable|exists:job_assignments,id',
            'assignment_task_id' => 'nullable|exists:assignment_tasks,id', // Added for task context
            'admin_recipient_id' => 'required_without_all:conversation_id,job_assignment_id|exists:admins,id', // Required if not part of existing flow
            'subject' => 'required_if:admin_recipient_id,!=,null|string|max:255',
            'content' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|max:5120', // 5MB max per file
        ]);

        $user = Auth::user();
        $conversation = null;

        if (isset($validated['conversation_id'])) {
            $conversation = Conversation::findOrFail($validated['conversation_id']);
            // Ensure the authenticated freelancer is part of the conversation
            if ($conversation->participant1_id !== $user->id && $conversation->participant2_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
        } elseif (isset($validated['job_assignment_id'])) {
            $jobAssignment = JobAssignment::findOrFail($validated['job_assignment_id']);
            // Ensure the authenticated freelancer is assigned to this job
            if ($jobAssignment->freelancer_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
            // Find or create a conversation for this job assignment (freelancer to client, mediated by admin)
            $conversation = Conversation::firstOrCreate([
                'job_id' => $jobAssignment->job_id,
                'job_assignment_id' => $jobAssignment->id,
                // Assuming participant1 is freelancer, participant2 is client for assignment messages
                // This might need adjustment based on how conversations are structured
                'participant1_id' => $user->id, 
                'participant1_type' => get_class($user),
                'participant2_id' => $jobAssignment->job->user_id, 
                'participant2_type' => get_class($jobAssignment->job->user),
            ], [
                'last_message_at' => now(),
            ]);
        } elseif (isset($validated['admin_recipient_id'])) {
            // Create a new conversation directly with an admin
            $adminUser = \App\Models\Admin::find($validated['admin_recipient_id']);
            if(!$adminUser){ $adminUser = \App\Models\Admin::first(); } // Fallback to first admin
            if(!$adminUser){ return redirect()->back()->with('error', 'No admin available to send the message to.');}

            $subject = $validated['subject'];
            $content = $validated['content'];

            if(isset($validated['job_assignment_id'])) {
                $relatedAssignment = JobAssignment::with('job')->find($validated['job_assignment_id']);
                if ($relatedAssignment && $relatedAssignment->freelancer_id === $user->id) {
                    $subject .= " (Regarding Assignment: " . $relatedAssignment->job->title . " - ID: " . $relatedAssignment->id;
                    if (isset($validated['assignment_task_id'])) {
                        $relatedTask = \App\Models\AssignmentTask::find($validated['assignment_task_id']);
                        if ($relatedTask && $relatedTask->job_assignment_id === $relatedAssignment->id) {
                            $subject .= " / Task: " . $relatedTask->title . " - ID: " . $relatedTask->id;
                        }
                    }
                    $subject .= ")";
                }
            }            $conversation = Conversation::firstOrCreate([
                'participant1_id' => $user->id,
                'participant1_type' => get_class($user),
                'participant2_id' => $adminUser->id,
                'participant2_type' => get_class($adminUser),
                // Link to job if assignment is provided, otherwise null for general messages
                'job_id' => isset($validated['job_assignment_id']) && $relatedAssignment ? $relatedAssignment->job_id : null,
                'job_assignment_id' => $validated['job_assignment_id'] ?? null,
            ],[
                'subject' => $subject, // Use potentially modified subject
                'last_message_at' => now(),
            ]);
            // Update subject if conversation already existed but subject was different
            if($conversation->subject !== $subject){
                $conversation->subject = $subject;
                $conversation->save();
             }
        }

        if (!$conversation) {
            return redirect()->back()->with('error', 'Could not determine the conversation for the message.');
        }
        
        // Create the new message
        $messageContent = $validated['content'];
        // If task_id was provided for an admin message, prepend it to content for clarity
        if (isset($validated['admin_recipient_id']) && isset($validated['assignment_task_id']) && !isset($validated['job_assignment_id'])) {
             // This case should ideally not happen if UI forces assignment selection before task.
             // But if it does, we can still add task context.
            $relatedTask = \App\Models\AssignmentTask::find($validated['assignment_task_id']);
            if($relatedTask) {
                $messageContent = "[Regarding Task: " . $relatedTask->title . " (ID: " . $relatedTask->id . ")]\n\n" . $validated['content'];
            }
        }


        $message = new Message([
            'conversation_id' => $conversation->id,
            'user_id' => $user->id,
            'content' => $messageContent, // Use potentially modified content
            'status' => 'pending', // Messages need admin approval before being visible to the client
        ]);
        
        $message->save();
        
        // Handle file attachments if any
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('message_attachments', 'public');
                $message->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }
        
        // Update the conversation's last message time
        $conversation->update(['last_message_at' => now()]);
        
        // Dispatch event for any listeners (e.g., notifications)
        event(new FreelancerMessageCreated($message));

        if (isset($validated['admin_recipient_id'])) {
            return redirect()->route('freelancer.messages.index')
                         ->with('success', 'Your message to the administrator has been sent and is pending approval.');
        } elseif ($conversation->job_assignment_id) {
            return redirect()->route('freelancer.assignments.show', $conversation->job_assignment_id)
                         ->with('success', 'Your message has been sent and is pending admin approval.');
        } else {
            // Fallback if no job_assignment_id and not an admin message (should ideally not happen with current logic)
            return redirect()->route('freelancer.messages.index')
                         ->with('success', 'Your message has been sent and is pending admin approval.');
        }
    }

    /**
     * Show the form for creating a new message to an admin regarding a specific job.
     */
    public function createAdminMessageForJob(Job $job): View
    {
        // Pass the job to the view. The view will handle selecting an admin (or it's predefined).
        return view('freelancer.messages.create-admin-for-job', compact('job'));
    }

    /**
     * Store a newly created message to an admin regarding a specific job.
     */
    public function storeAdminMessageForJob(Request $request, Job $job): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|max:5120', // 5MB max per file
        ]);

        $freelancer = Auth::user();
        
        // For simplicity, let's assume messages about a job go to the first admin.
        // A more robust system might have a dedicated support admin or allow selection.
        $adminRecipient = Admin::first();

        if (!$adminRecipient) {
            return redirect()->route('freelancer.jobs.show', $job)->with('error', 'No admin available to send the message to.');
        }

        $subject = "Question regarding Job: \"{$job->title}\" (ID: {$job->id})";

        // Find or create a conversation between the freelancer and the admin, specific to this job query
        $conversation = Conversation::firstOrCreate(
            [
                'participant1_id' => $freelancer->id,
                'participant1_type' => get_class($freelancer),
                'participant2_id' => $adminRecipient->id,
                'participant2_type' => get_class($adminRecipient),
                'job_id' => $job->id, // Link conversation to the job
                'subject' => $subject, // Use a specific subject for job queries
            ],
            [
                'last_message_at' => now(),
            ]
        );
         // Update subject if conversation already existed but subject was different (e.g. if job_id was null before)
         if($conversation->subject !== $subject || $conversation->job_id !== $job->id){
            $conversation->subject = $subject;
            $conversation->job_id = $job->id; // Ensure job_id is set
            $conversation->save();
         }


        $message = new Message([
            'conversation_id' => $conversation->id,
            'user_id' => $freelancer->id, // Message is from the freelancer
            'content' => $validated['content'],
            'status' => 'pending', // Messages to admin might also need review or just go through
        ]);
        $message->save();

        // Handle file attachments if any
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('message_attachments', 'public'); // Ensure 'public' disk is configured
                $message->attachments()->create([
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }

        $conversation->update(['last_message_at' => now()]);

        // Dispatch event for admin notification
        event(new FreelancerMessageCreated($message)); // Assuming this event is generic enough

        return redirect()->route('freelancer.jobs.show', $job)->with('success', 'Your message regarding the job has been sent to the admin.');
    }
}
