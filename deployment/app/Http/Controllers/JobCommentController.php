<?php

namespace App\Http\Controllers;

use App\Events\JobCommentCreated;
use App\Events\JobCommentStatusUpdated;
use App\Models\Job;
use App\Models\JobComment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Storage; // Added

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
        try {
            $user = auth()->user();
            $isAdmin = auth()->guard('admin')->check();

            // Allow clients or admins to comment
            if (!$isAdmin && !$user->hasRole('client')) {
                abort(403, 'Only clients or admins can create comments.');
            }

            // If admin, they can view any job. If client, authorize 'view' policy.
            if (!$isAdmin) {
                $this->authorize('view', $job);
            }

            // Verify the job exists and is active
            if (!$job->exists || $job->trashed()) {
                return response()->json(['message' => 'Job not found or inactive'], Response::HTTP_NOT_FOUND);
            }

            $validated = $request->validate([
                'content' => 'required|string|max:1000',
                'parent_comment_id' => 'nullable|exists:job_comments,id',
                'work_submission_id' => 'nullable|exists:work_submissions,id',
                'screenshot' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048'
            ]);

            $commenterId = $isAdmin ? auth()->guard('admin')->id() : $user->id;
            $commenterType = $isAdmin ? Admin::class : User::class;

            $screenshotPath = null;
            if ($request->hasFile('screenshot')) {
                try {
                    $screenshotPath = $request->file('screenshot')->store('public/comment_screenshots');
                    $screenshotPath = str_replace('public/', '', $screenshotPath);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to upload comment screenshot: ' . $e->getMessage());
                    // Continue without the screenshot if upload fails
                }
            }

            // Start database transaction
            \DB::beginTransaction();

            try {
                $comment = $job->comments()->create([
                    'user_id' => $commenterId,
                    'user_type' => $commenterType,
                    'comment_text' => $validated['content'],
                    'parent_comment_id' => $validated['parent_comment_id'] ?? null,
                    'work_submission_id' => $validated['work_submission_id'] ?? null,
                    'screenshot_path' => $screenshotPath,
                    'status' => JobComment::STATUS_NEW
                ]);

                // Broadcast the event within try-catch
                try {
                    JobCommentCreated::dispatch($comment);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to dispatch JobCommentCreated event: ' . $e->getMessage());
                    // Continue even if event dispatch fails
                }

                \DB::commit();

                // Load necessary relationships
                $comment->load(['user', 'job.jobAssignment']);

                // Redirect back for admin, return JSON for client
                if ($isAdmin) {
                    return redirect()
                        ->route('admin.jobs.show', $job)
                        ->with('success', 'Comment posted successfully.');
                }

                return response()->json($comment, Response::HTTP_CREATED);

            } catch (\Exception $e) {
                \DB::rollBack();
                
                // Delete uploaded file if it exists and transaction failed
                if ($screenshotPath) {
                    Storage::delete('public/' . $screenshotPath);
                }
                
                throw $e;
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error creating comment: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Failed to create comment. Please try again.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Mark a comment as discussed by freelancer.
     */
    public function markAsDiscussed(JobComment $comment)
    {
        try {
            if (!auth()->user()->hasRole('freelancer')) {
                abort(403, 'Only freelancers can mark comments as discussed');
            }

            if (!$comment->exists || !$comment->job) {
                return response()->json(['message' => 'Comment not found or invalid'], Response::HTTP_NOT_FOUND);
            }

            $this->authorize('update', $comment->job);

            $oldStatus = $comment->status;

            \DB::beginTransaction();
            try {
                if (!$comment->markAsDiscussed()) {
                    throw new \Exception('Failed to update comment status');
                }

                try {
                    JobCommentStatusUpdated::dispatch($comment, $oldStatus);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to dispatch JobCommentStatusUpdated event: ' . $e->getMessage());
                    // Continue even if event dispatch fails
                }

                \DB::commit();

                return response()->json($comment->fresh(['job', 'user']));

            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error marking comment as discussed: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Failed to update comment status. Please try again.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Mark a comment as pending freelancer action by admin.
     */
    public function markAsPendingFreelancer(JobComment $comment)
    {
        try {
            if (!auth()->user()->hasRole('admin')) {
                abort(403, 'Only admins can mark comments as pending freelancer action');
            }

            if (!$comment->exists || !$comment->job) {
                return response()->json(['message' => 'Comment not found or invalid'], Response::HTTP_NOT_FOUND);
            }

            $oldStatus = $comment->status;

            \DB::beginTransaction();
            try {
                if (!$comment->markAsPendingFreelancer()) {
                    throw new \Exception('Failed to update comment status');
                }

                try {
                    JobCommentStatusUpdated::dispatch($comment, $oldStatus);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to dispatch JobCommentStatusUpdated event: ' . $e->getMessage());
                    // Continue even if event dispatch fails
                }

                \DB::commit();

                return response()->json($comment->fresh(['job', 'user']));

            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error marking comment as pending freelancer: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Failed to update comment status. Please try again.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Mark a comment as resolved by admin.
     */
    public function markAsResolved(JobComment $comment)
    {
        try {
            if (!auth()->user()->hasRole('admin')) {
                abort(403, 'Only admins can mark comments as resolved');
            }

            if (!$comment->exists || !$comment->job) {
                return response()->json(['message' => 'Comment not found or invalid'], Response::HTTP_NOT_FOUND);
            }

            $oldStatus = $comment->status;

            \DB::beginTransaction();
            try {
                if (!$comment->markAsResolved()) {
                    throw new \Exception('Failed to update comment status');
                }

                try {
                    JobCommentStatusUpdated::dispatch($comment, $oldStatus);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to dispatch JobCommentStatusUpdated event: ' . $e->getMessage());
                    // Continue even if event dispatch fails
                }

                \DB::commit();

                return response()->json($comment->fresh(['job', 'user']));

            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error marking comment as resolved: ' . $e->getMessage());
            return response()->json(
                ['message' => 'Failed to update comment status. Please try again.'],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
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
