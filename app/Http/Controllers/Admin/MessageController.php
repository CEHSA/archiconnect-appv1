<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageApprovedByAdmin;
use App\Events\MessageRejectedByAdmin; // Import the new event for rejection
use App\Events\MessageReviewedByAdmin; // Import the new event
use App\Http\Controllers\Controller;
// use App\Models\Admin; // Removed Admin model import, assuming admins are Users
use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use App\Models\AdminActivityLog; // Import AdminActivityLog
use App\Events\AdminMessageSent; // Added
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all conversations, eager load participants and job
        // Admins can see all conversations
        $conversations = Conversation::with(['participants', 'job', 'messages' => function ($query) {
                $query->latest()->limit(1); // For displaying latest message snippet
            }])
            ->latest('last_message_at')
            ->paginate(15);

        // Get pending messages for review
        $pendingMessages = Message::where('status', 'pending')
            ->with(['user', 'conversation'])
            ->latest()
            ->take(5)
            ->get();

        // Get message review activity logs
        $messageActivityLogs = AdminActivityLog::where('action_type', 'like', 'message_%')
            ->orWhere('loggable_type', Message::class) // To catch other message related logs if any
            ->with(['admin', 'loggable']) // Eager load admin and the message (if loggable is Message)
            ->latest()
            ->take(15) // Fetch latest 15 log entries
            ->get();

        return view('admin.messages.index', compact('conversations', 'pendingMessages', 'messageActivityLogs'));
    }

    /**
     * Display the specified conversation with its messages.
     */
    public function showConversation(Conversation $conversation)
    {
        // Load the conversation with its messages and participants
        $conversation->load(['messages.user', 'messages.attachments', 'participants', 'job']);

        // Mark messages as read for the current admin user
        $adminUser = Auth::user(); // Auth::user() should return the authenticated Admin (User model instance)
        if ($adminUser && $adminUser->role === User::ROLE_ADMIN) {
            $conversation->markAsReadForUser($adminUser);
        }

        return view('admin.messages.conversation', compact('conversation'));
    }

    /**
     * Show the form for creating a new message in conversation.
     */
    public function create(Request $request, ?Conversation $conversation = null)
    {
        // If conversation is not directly injected, try to load from request query parameter
        if (!$conversation && $request->has('conversation_id')) {
            $conversation = Conversation::find($request->input('conversation_id'));
        }
        
        // If no conversation specified (even after checking request), allow creating a new conversation
        if (!$conversation) {
            // Users excluding other admins, or specific roles like client/freelancer
            $users = User::where('role', '!=', User::ROLE_ADMIN)->orderBy('name')->get(); 
            $jobs = Job::orderBy('title')->get(); // Get jobs for linking
            return view('admin.messages.create', compact('users', 'jobs'));
        }

        // Otherwise, show the form to add a message to an existing conversation
        $conversation->load(['participants', 'job']);
        return view('admin.messages.create', compact('conversation'));
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => 'nullable|exists:conversations,id',
            'recipient_id' => 'required_without:conversation_id|exists:users,id',
            'job_id' => 'nullable|exists:jobs,id', // Add job linking
            'content' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|max:5120', // 5MB max per file
        ]);

        $adminUser = Auth::user(); // Authenticated Admin (User model)

        // If no conversation ID is provided, create a new conversation
        if (!isset($validated['conversation_id'])) {
            $recipient = User::findOrFail($validated['recipient_id']);

            $conversation = Conversation::create([
                'created_by_user_id' => $adminUser->id,
                'job_id' => $validated['job_id'] ?? null,
                'subject' => $request->input('subject', 'Conversation with ' . $recipient->name), // Add subject if provided
                'last_message_at' => now(),
            ]);
            // Add admin and recipient as participants
            $conversation->participants()->attach([$adminUser->id, $recipient->id]);
        } else {
            $conversation = Conversation::findOrFail($validated['conversation_id']);
            // Admin can reply to any conversation, or check if they are a participant if stricter rules apply.
            // For now, if an admin is accessing this, they can reply.
            // if (!$conversation->isParticipant($adminUser) && !$adminUser->isSuperAdmin()) { // More specific check
            //     abort(403, 'Unauthorized to send message in this conversation.');
            // }
        }

        $message = $conversation->messages()->create([
            'user_id' => $adminUser->id,
            'body' => $validated['content'],
            'admin_review_status' => 'approved', 
        ]);

        // Handle file attachments if any
        if ($request->hasFile('attachments')) {
            $jobId = $conversation->job_id ?? 'general'; // Fallback if no job_id
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

        event(new AdminMessageSent($message)); // Dispatch the event

        return redirect()->route('admin.messages.showConversation', $conversation)
            ->with('success', 'Your message has been sent.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        // Eager load relationships if not already loaded by route model binding with('user', 'conversation')
        $message->loadMissing(['user', 'conversation']);

        return view('admin.messages.show', compact('message'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message)
    {
        $request->validate([
            'status' => ['required', 'string', 'in:approved,rejected'],
            'admin_remarks' => ['nullable', 'string'],
        ]);

        $message->update([
            'status' => $request->status,
            'reviewed_by_admin_id' => Auth::id(),
            'admin_remarks' => $request->admin_remarks,
        ]);

        /** @var \App\Models\Admin $adminUser */
        $adminUser = Auth::guard('admin')->user();

        if ($adminUser) {
            // Dispatch the generic review event for logging (covers both approved and rejected)
            event(new MessageReviewedByAdmin($message, $adminUser, $request->status));

            // Dispatch specific event for notification based on status
            if ($message->status === 'approved') {
                event(new MessageApprovedByAdmin($message)); // Existing event for approval notifications
            } elseif ($message->status === 'rejected') {
                event(new MessageRejectedByAdmin($message, $adminUser, $request->admin_remarks));
            }
        } else {
            // Log a warning if admin user cannot be determined, though Auth middleware should prevent this.
            Log::warning("Admin user not found when trying to dispatch message review events for Message ID: {$message->id}");
        }

        return redirect()->route('admin.messages.index')->with('success', 'Message reviewed successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }

    /**
     * Display a history of message review activities.
     */
    public function history(Request $request)
    {
        $usersForFilter = User::whereIn('role', [User::ROLE_CLIENT, User::ROLE_FREELANCER])->orderBy('name')->get();
        $adminsForFilter = User::where('role', User::ROLE_ADMIN)->orderBy('name')->get(); // Changed Admin:: to User::

        $query = AdminActivityLog::where(function($q) {
                $q->where('action_type', 'like', 'message_%')
                  ->orWhere('loggable_type', Message::class);
            })
            ->with(['admin', 'loggable.user']); // Eager load admin and the message's original sender

        if ($request->filled('admin_id')) {
            $query->where('admin_id', $request->input('admin_id'));
        }

        if ($request->filled('user_id')) {
            $userId = $request->input('user_id');
            $query->whereHasMorph('loggable', [Message::class], function ($qMessage) use ($userId) {
                $qMessage->where('user_id', $userId); // Filter by sender of the original message
            });
        }
        
        if ($request->filled('user_group')) {
            $userRole = $request->input('user_group');
            $query->whereHasMorph('loggable', [Message::class], function ($qMessage) use ($userRole) {
                $qMessage->whereHas('user', function($qUser) use ($userRole) {
                    $qUser->where('role', $userRole); // Filter by role of the sender of the original message
                });
            });
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $messageActivityLogs = $query->latest()->paginate(20)->withQueryString();

        return view('admin.messages.history', compact('messageActivityLogs', 'usersForFilter', 'adminsForFilter'));
    }
}
