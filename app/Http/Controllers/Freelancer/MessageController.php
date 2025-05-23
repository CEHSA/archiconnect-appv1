<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Job;
use App\Models\JobAssignment;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\FreelancerMessageCreated; 
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
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
        if (!$conversation->isParticipant(Auth::user())) {
            abort(403, 'Unauthorized action.');
        }
        $conversation->load(['messages.user', 'messages.attachments', 'participants', 'job']);
        $conversation->markAsReadForUser(Auth::user());
        return view('freelancer.messages.show', compact('conversation'));
    }

    /**
     * Show the form for creating a new message related to a JobAssignment.
     */
    public function create(JobAssignment $assignment)
    {
        $user = Auth::user();
        if ($assignment->freelancer_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $conversation = Conversation::firstOrCreate(
            ['job_assignment_id' => $assignment->id],
            [
                'job_id' => $assignment->job_id,
                'created_by_user_id' => $user->id,
                'subject' => 'Conversation for Assignment: ' . $assignment->job->title,
                'last_message_at' => now(),
            ]
        );

        $participants = [$user->id, $assignment->job->user_id]; 
        $adminUsers = User::where('role', User::ROLE_ADMIN)->pluck('id')->toArray();
        $participants = array_unique(array_merge($participants, $adminUsers));
        $conversation->participants()->syncWithoutDetaching($participants);

        return view('freelancer.assignments.messages.create', compact('assignment', 'conversation'));
    }
    
    /**
     * Show the form for creating a new message to an admin (general or assignment/task related).
     */
    public function createAdminMessage()
    {
        $freelancer = Auth::user();
        $assignments = JobAssignment::where('freelancer_id', $freelancer->id)
            ->with(['job', 'tasks' => function ($query) {
                $query->orderBy('title');
            }])
            ->whereIn('status', ['accepted', 'in_progress', 'revision_requested'])
            ->get();

        $assignmentOptions = $assignments->map(function ($assignment) {
            return [
                'id' => $assignment->id,
                'job_title' => $assignment->job->title,
                'tasks' => $assignment->tasks->map(function ($task) {
                    return ['id' => $task->id, 'title' => $task->title];
                })->all(),
            ];
        })->all();
        
        $admins = User::where('role', User::ROLE_ADMIN)->orderBy('name')->get(['id', 'name']);

        return view('freelancer.messages.create-admin', compact('assignmentOptions', 'admins'));
    }


    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'conversation_id' => 'nullable|exists:conversations,id',
            'job_assignment_id' => 'nullable|exists:job_assignments,id',
            'assignment_task_id' => 'nullable|exists:assignment_tasks,id',
            'admin_recipient_id' => [
                'required_without_all:conversation_id,job_assignment_id',
                'nullable',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if ($value && !User::where('id', $value)->where('role', User::ROLE_ADMIN)->exists()) {
                        $fail('The selected admin recipient is not a valid admin.');
                    }
                },
            ],
            'subject' => 'required_if:admin_recipient_id,!=,null|string|max:255',
            'content' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|max:5120',
        ]);

        $user = Auth::user();
        $conversation = null;
        $messageBody = $validated['content']; 

        if (isset($validated['conversation_id'])) {
            $conversation = Conversation::findOrFail($validated['conversation_id']);
            if (!$conversation->isParticipant($user)) {
                abort(403, 'Unauthorized action.');
            }
        } elseif (isset($validated['job_assignment_id'])) {
            $jobAssignment = JobAssignment::with('job.user')->findOrFail($validated['job_assignment_id']);
            if ($jobAssignment->freelancer_id !== $user->id) {
                abort(403, 'Unauthorized action.');
            }
            $conversation = Conversation::firstOrCreate(
                ['job_assignment_id' => $jobAssignment->id],
                [
                    'job_id' => $jobAssignment->job_id,
                    'created_by_user_id' => $user->id,
                    'subject' => $validated['subject'] ?? 'Conversation for Assignment: ' . $jobAssignment->job->title,
                    'last_message_at' => now(),
                ]
            );
            $participants = [$user->id, $jobAssignment->job->user_id];
            $adminUsers = User::where('role', User::ROLE_ADMIN)->pluck('id')->toArray();
            $participants = array_unique(array_merge($participants, $adminUsers));
            $conversation->participants()->syncWithoutDetaching($participants);

        } elseif (isset($validated['admin_recipient_id'])) {
            $adminRecipient = User::where('id', $validated['admin_recipient_id'])->where('role', User::ROLE_ADMIN)->first();
            if(!$adminRecipient) {
                return redirect()->back()->with('error', 'Selected admin recipient is invalid.');
            }
            
            $subject = $validated['subject'];
            $jobIdForConvo = null;
            $jobAssignmentIdForConvo = null;

            if (isset($validated['job_assignment_id'])) {
                $relatedAssignment = JobAssignment::with('job')->find($validated['job_assignment_id']);
                if ($relatedAssignment && $relatedAssignment->freelancer_id === $user->id) {
                    $jobIdForConvo = $relatedAssignment->job_id;
                    $jobAssignmentIdForConvo = $relatedAssignment->id;
                    $subjectPrefix = "Assignment: " . $relatedAssignment->job->title . " (ID: " . $relatedAssignment->id;
                    if (isset($validated['assignment_task_id'])) {
                        $relatedTask = \App\Models\AssignmentTask::find($validated['assignment_task_id']);
                        if ($relatedTask && $relatedTask->job_assignment_id === $relatedAssignment->id) {
                            $subjectPrefix .= " / Task: " . $relatedTask->title;
                        }
                    }
                    $subject = $subjectPrefix . ") - " . $subject;
                }
            }
            
            $conversation = Conversation::firstOrCreate(
                [
                    'subject' => $subject, 
                    'created_by_user_id' => $user->id,
                    'job_id' => $jobIdForConvo,
                    'job_assignment_id' => $jobAssignmentIdForConvo,
                ],
                ['last_message_at' => now()]
            );
            $conversation->participants()->syncWithoutDetaching(array_unique([$user->id, $adminRecipient->id]));
        }

        if (!$conversation) {
            return redirect()->back()->with('error', 'Could not determine or create the conversation.');
        }
        
        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'body' => $messageBody, 
            'admin_review_status' => 'pending_review',
        ]);
        
        if ($request->hasFile('attachments')) {
            $jobId = $conversation->job_id ?? ($conversation->jobAssignment->job_id ?? 'general');
            $storagePath = "ArchiAxis/Job_{$jobId}/chat_thread";
            foreach ($request->file('attachments') as $file) {
                $path = $file->store($storagePath, 'public');
                $message->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }
        
        $conversation->update(['last_message_at' => $message->created_at]);
        event(new FreelancerMessageCreated($message));

        if (isset($validated['admin_recipient_id']) && !$jobAssignmentIdForConvo) {
             return redirect()->route('freelancer.messages.index')->with('success', 'Message to admin sent and pending approval.');
        } elseif ($conversation->job_assignment_id) {
            return redirect()->route('freelancer.assignments.show', $conversation->job_assignment_id)->with('success', 'Message sent and pending admin approval.');
        } else { 
            return redirect()->route('freelancer.messages.show', $conversation)->with('success', 'Message sent and pending admin approval.');
        }
    }

    /**
     * Show the form for creating a new message to an admin regarding a specific job.
     */
    public function createAdminMessageForJob(Job $job): View
    {
        return view('freelancer.messages.create-admin-for-job', compact('job'));
    }

    /**
     * Store a newly created message to an admin regarding a specific job.
     */
    public function storeAdminMessageForJob(Request $request, Job $job): RedirectResponse
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|max:5120',
        ]);

        $freelancer = Auth::user();
        $adminUsers = User::where('role', User::ROLE_ADMIN)->get();

        if ($adminUsers->isEmpty()) {
            return redirect()->route('freelancer.jobs.show', $job)->with('error', 'No admin available.');
        }

        $subject = "Question regarding Job: \"{$job->title}\" (ID: {$job->id})";
        $conversation = Conversation::firstOrCreate(
            ['job_id' => $job->id, 'subject' => $subject],
            ['created_by_user_id' => $freelancer->id, 'last_message_at' => now()]
        );
        
        $participantIds = $adminUsers->pluck('id')->toArray();
        $participantIds[] = $freelancer->id;
        $conversation->participants()->syncWithoutDetaching(array_unique($participantIds));
        
        $message = $conversation->messages()->create([
            'user_id' => $freelancer->id,
            'body' => $validated['content'],
            'admin_review_status' => 'pending_review',
        ]);

        if ($request->hasFile('attachments')) {
            $jobId = $conversation->job_id ?? 'general'; // job_id is guaranteed here from firstOrCreate
            $storagePath = "ArchiAxis/Job_{$jobId}/chat_thread";
            foreach ($request->file('attachments') as $file) {
                $path = $file->store($storagePath, 'public'); 
                $message->attachments()->create([
                    'file_path' => $path,
                    'original_name' => $file->getClientOriginalName(),
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ]);
            }
        }
        $conversation->update(['last_message_at' => $message->created_at]);
        event(new FreelancerMessageCreated($message));

        return redirect()->route('freelancer.jobs.show', $job)->with('success', 'Message sent to admin.');
    }
}
