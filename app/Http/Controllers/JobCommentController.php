<?php

namespace App\Http\Controllers;

use App\Events\JobCommentCreated;
use App\Events\JobCommentStatusUpdated;
use App\Models\Job;
use App\Models\JobComment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JobCommentController extends Controller
{
    /**
     * Display a listing of comments for a specific job.
     */
    public function index(Job $job)
    {
        // Only allow access to job owner (client), assigned freelancer, or admin
        $this->authorize('view', $job);

        $comments = $job->comments()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return response()->json($comments);
    }

    /**
     * Store a newly created comment.
     */
    public function store(Request $request, Job $job)
    {
        // Only clients can create new comments
        if (!auth()->user()->hasRole('client')) {
            abort(403, 'Only clients can create new comments');
        }

        $this->authorize('view', $job);

        $validated = $request->validate([
            'comment_text' => 'required|string|max:1000',
            'parent_comment_id' => 'nullable|exists:job_comments,id'
        ]);

        $comment = $job->comments()->create([
            'user_id' => auth()->id(),
            'comment_text' => $validated['comment_text'],
            'parent_comment_id' => $validated['parent_comment_id'] ?? null,
            'status' => JobComment::STATUS_NEW
        ]);

        // Broadcast the event
        JobCommentCreated::dispatch($comment);

        return response()->json($comment->load('user'), Response::HTTP_CREATED);
    }

    /**
     * Mark a comment as discussed by freelancer.
     */
    public function markAsDiscussed(JobComment $comment)
    {
        if (!auth()->user()->hasRole('freelancer')) {
            abort(403, 'Only freelancers can mark comments as discussed');
        }

        $this->authorize('update', $comment->job);

        $oldStatus = $comment->status;
        
        if ($comment->markAsDiscussed()) {
            JobCommentStatusUpdated::dispatch($comment, $oldStatus);
            return response()->json($comment->fresh());
        }

        return response()->json(['message' => 'Failed to update status'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Mark a comment as pending freelancer action by admin.
     */
    public function markAsPendingFreelancer(JobComment $comment)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Only admins can mark comments as pending freelancer action');
        }

        $oldStatus = $comment->status;
        
        if ($comment->markAsPendingFreelancer()) {
            JobCommentStatusUpdated::dispatch($comment, $oldStatus);
            return response()->json($comment->fresh());
        }

        return response()->json(['message' => 'Failed to update status'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Mark a comment as resolved by admin.
     */
    public function markAsResolved(JobComment $comment)
    {
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Only admins can mark comments as resolved');
        }

        $oldStatus = $comment->status;
        
        if ($comment->markAsResolved()) {
            JobCommentStatusUpdated::dispatch($comment, $oldStatus);
            return response()->json($comment->fresh());
        }

        return response()->json(['message' => 'Failed to update status'], Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Get comments that need attention.
     */
    public function needsAttention()
    {
        // For admins: all comments needing attention
        if (auth()->user()->hasRole('admin')) {
            $comments = JobComment::needsAttention()
                ->with(['job', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }
        // For freelancers: comments on their jobs that need attention
        elseif (auth()->user()->hasRole('freelancer')) {
            $comments = JobComment::whereHas('job', function ($query) {
                    $query->where('freelancer_id', auth()->id());
                })
                ->needsAttention()
                ->with(['job', 'user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }
        // For clients: just return empty (they don't need this view)
        else {
            return response()->json(['data' => []]);
        }

        return response()->json($comments);
    }
}
