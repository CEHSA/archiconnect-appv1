<?php

namespace App\Http\Controllers\Admin;

use App\Events\MessageApprovedByAdmin;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;

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

        return view('admin.messages.index', compact('conversations', 'pendingMessages'));
    }

    /**
     * Display the specified conversation with its messages.
     */
    public function showConversation(Conversation $conversation)
    {
        // Load the conversation with its messages and participants
        $conversation->load(['messages.user', 'participant1', 'participant2', 'job']);

        return view('admin.messages.conversation', compact('conversation'));
    }

    /**
     * Show the form for creating a new message in conversation.
     */
    public function create(Conversation $conversation = null)
    {
        // If no conversation specified, allow creating a new conversation
        if (!$conversation) {
            $users = User::where('role', '!=', User::ROLE_ADMIN)->get();
            return view('admin.messages.create', compact('users'));
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
            'content' => 'required|string|max:1000',
            'attachments.*' => 'nullable|file|max:5120', // 5MB max per file
        ]);

        // If no conversation ID is provided, create a new conversation
        if (!isset($validated['conversation_id'])) {
            $recipient = User::findOrFail($validated['recipient_id']);
            
            // Create a new conversation
            $conversation = Conversation::create([
                'participant1_id' => auth()->id(),
                'participant2_id' => $recipient->id,
                'last_message_at' => now(),
            ]);
        } else {
            $conversation = Conversation::findOrFail($validated['conversation_id']);
        }

        // Create the new message
        $message = new Message([
            'conversation_id' => $conversation->id,
            'user_id' => auth()->id(),
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
                    'original_file_name' => $file->getClientOriginalName(),
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
            'reviewed_by_admin_id' => auth()->id(),
            'admin_remarks' => $request->admin_remarks,
        ]);

        if ($message->status === 'approved') {
            event(new MessageApprovedByAdmin($message));
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
}
