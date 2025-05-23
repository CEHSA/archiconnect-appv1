<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\ClientMessageSent; // Changed from ClientMessageCreated

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
        if (!$conversation->isParticipant(Auth::user())) {
            abort(403, 'Unauthorized action.');
        }

        // Load the conversation with its messages and participants
        $conversation->load(['messages.user', 'messages.attachments', 'participants', 'job']); // Load 'participants' instead of participant1/2

        // Mark messages as read for the current user
        $conversation->markAsReadForUser(Auth::user());

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
        if (!$conversation->isParticipant(Auth::user())) {
            abort(403, 'Unauthorized action.');
        }

        $message = $conversation->messages()->create([ // Create message via relationship
            'user_id' => Auth::id(),
            'body' => $request->content, // Assuming 'body' is the field in 'messages' table, was 'content'
            'admin_review_status' => 'approved', // Client messages are pre-approved
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

        // Update the conversation's last_message_at timestamp
        $conversation->update(['last_message_at' => $message->created_at]); // Use message's creation time

        event(new ClientMessageSent($message)); // Dispatch the event

        return redirect()->route('client.messages.show', $conversation)
            ->with('success', 'Message sent successfully.');
    }
}
