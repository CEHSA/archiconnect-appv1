<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\ClientMessageCreated; // Moved to bottom to avoid conflict if needed later

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get all conversations where the client is a participant
        $conversations = Conversation::forUser(Auth::user())
            ->with(['job', 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->latest('last_message_at')
            ->get();

        return view('client.messages.index', compact('conversations'));
    }

    /**
     * Display the specified conversation with its messages.
     */
    public function show(Conversation $conversation)
    {
        // Ensure the authenticated client is part of the conversation
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

        return view('client.messages.show', compact('conversation'));
    }

    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'conversation_id' => ['required', 'exists:conversations,id'],
            'content' => ['required', 'string', 'max:1000'],
            'attachments.*' => 'nullable|file|max:5120', // 5MB max per file
        ]);

        // Ensure the authenticated client is part of the conversation
        $conversation = Conversation::findOrFail($request->conversation_id);
        if ($conversation->participant1_id !== Auth::id() && $conversation->participant2_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'user_id' => Auth::id(),
            'content' => $request->content,
            'status' => 'approved', // Client messages don't need approval
        ]);

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

        // Update the conversation's last_message_at timestamp
        $conversation->update(['last_message_at' => now()]);

        return redirect()->route('client.messages.show', $conversation)
            ->with('success', 'Message sent successfully.');
    }
}
