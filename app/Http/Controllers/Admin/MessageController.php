<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageApprovedByAdmin;
use App\Events\MessageRejectedByAdmin; // Import the new event for rejection
use App\Events\MessageReviewedByAdmin; // Import the new event
use App\Http\Controllers\Controller;
use App\Models\Admin; // Import Admin model
use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use App\Models\AdminActivityLog; // Import AdminActivityLog
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all conversations
        $conversations = Conversation::with(['participant1', 'participant2', 'job'])
            ->latest('last_message_at')
            ->paginate(10);

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
        $conversation->load(['messages.user', 'participant1', 'participant2', 'job']);

        // Mark messages as read for the current admin user
        /** @var \App\Models\User|null $adminUser */
        $adminUser = Auth::user();
        if ($adminUser) {
            foreach ($conversation->messages as $message) {
                // Check if the message was not sent by the current admin and is unread
                if ($message->user_id !== $adminUser->id && $message->isUnread()) {
                    $message->markAsRead();
                }
            }
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
            $users = User::where('role', '!=', User::ROLE_ADMIN)->get();
            $jobs = Job::with('user')->orderBy('created_at', 'desc')->get(); // Get jobs for linking
            return view('admin.messages.create', compact('users', 'jobs'));
        }

        // Otherwise, show the form to add a message to an existing conversation
        $conversation->load(['participant1', 'participant2', 'job']);
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

        // If no conversation ID is provided, create a new conversation
        if (!isset($validated['conversation_id'])) {
            $recipient = User::findOrFail($validated['recipient_id']);

            // Create a new conversation
            $conversation = Conversation::create([
                'participant1_id' => Auth::id(),
                'participant1_type' => get_class(Auth::user()),
                'participant2_id' => $recipient->id,
                'participant2_type' => get_class($recipient),
                'job_id' => $validated['job_id'] ?? null, // Link to job if provided
                'last_message_at' => now(),
            ]);
        } else {
            $conversation = Conversation::findOrFail($validated['conversation_id']);
        }

        // Create the new message
        $message = new Message([
            'conversation_id' => $conversation->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
            'status' => 'approved', // Admin messages are auto-approved
        ]);

        $message->save();

        // Handle file attachments if any
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('message_attachments', 'public');
                $message->attachments()->create([
                    'file_path' => $path,
                    'original_file_name' => $file->getClientOriginalName(), // Reverted to original_file_name
                    'file_size' => $file->getSize(),
                    'file_type' => $file->getMimeType(),
                ]);
            }
        }

        // Update the conversation's last message time
        $conversation->update(['last_message_at' => now()]);

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
        $adminsForFilter = Admin::orderBy('name')->get();

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
