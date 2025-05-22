<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\BriefingRequest;
use App\Models\WorkSubmission; // Keep this, it's used in clientDashboard and potentially for status mapping
use App\Models\JobAssignment;
use App\Models\Proposal;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role === 'freelancer') {
            return redirect()->route('freelancer.dashboard');
        } elseif ($user->role === 'client') {
            return redirect()->route('client.dashboard');
        }
        // Fallback for users with no specific role or if routes are hit directly
        // This could also be a generic landing page if preferred
        return view('dashboard'); // Generic dashboard view from Breeze
    }

    public function clientDashboard()
    {
        $client = Auth::user();
        $activeJobsCount = Job::where('user_id', $client->id)
            ->whereIn('status', ['pending_assignment', 'in_progress', 'pending_review'])
            ->count();
        $pendingBriefingRequestsCount = BriefingRequest::where('client_id', $client->id)
            ->where('status', 'pending_review')
            ->count();
        $submissionsAwaitingReviewCount = WorkSubmission::whereHas('jobAssignment.job', function ($query) use ($client) {
            $query->where('user_id', $client->id);
        })
        ->where('status', 'pending_client_review')
        ->count();

        // Dummy data for recent jobs until actual job data flow is complete
        // Added 'status_key' for the badge component
        $recentClientJobs = [
            ['name' => 'Kitchen Renovation Brief', 'status' => 'Awaiting Architect Assignment', 'status_key' => 'pending_assignment', 'date' => '06/28/2023'],
            ['name' => 'New Patio Design', 'status' => 'In Progress', 'status_key' => 'in_progress', 'date' => '06/25/2023'],
        ];

        return view('client.dashboard', compact(
            'activeJobsCount',
            'pendingBriefingRequestsCount',
            'submissionsAwaitingReviewCount',
            'recentClientJobs'
        ));
    }

    public function freelancerDashboard()
    {
        $freelancer = Auth::user();
        $freelancerId = $freelancer->id;

        // 1. New Job Opportunities
        // Jobs that are 'open' or 'approved' and the freelancer is not already assigned to.
        $newJobOpportunities = Job::with('client') // Eager load the client who posted the job
            ->whereIn('status', ['open', 'approved'])
            ->whereDoesntHave('assignments', function ($query) use ($freelancerId) {
                $query->where('freelancer_id', $freelancerId);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // 2. Active Jobs
        // JobAssignments for the freelancer that are in an active state.
        // This includes assignments directly 'in_progress' or having work submissions in various review stages.
        $activeJobAssignments = JobAssignment::with(['job.client', 'workSubmissions' => function ($query) {
            $query->orderBy('created_at', 'desc'); // Get the latest work submission first
        }])
            ->where('freelancer_id', $freelancerId)
            ->where(function ($query) {
                $query->where('status', 'in_progress') // Directly in progress
                      ->orWhere('status', 'pending_freelancer_acceptance') // Waiting for freelancer to accept
                      ->orWhereHas('workSubmissions', function ($subQuery) {
                          $subQuery->whereIn('status', [
                              WorkSubmission::STATUS_PENDING_SUBMISSION,
                              WorkSubmission::STATUS_SUBMITTED_FOR_ADMIN_REVIEW,
                              WorkSubmission::STATUS_ADMIN_REVISION_REQUESTED,
                              WorkSubmission::STATUS_PENDING_CLIENT_REVIEW,
                              WorkSubmission::STATUS_CLIENT_REVISION_REQUESTED,
                          ]);
                      });
            })
            ->whereDoesntHave('workSubmissions', function ($subQuery) { // Exclude if latest submission is approved/rejected
                $subQuery->whereIn('status', [
                    WorkSubmission::STATUS_APPROVED_BY_CLIENT,
                    WorkSubmission::STATUS_REJECTED,
                ])->latest();
            })
            ->orderBy('updated_at', 'desc')
            ->take(10) // Increased limit for active jobs
            ->get();

        // Map WorkSubmission statuses to JobAssignment for display
        $activeJobAssignments->each(function ($assignment) {
            if ($assignment->workSubmissions->isNotEmpty()) {
                $latestSubmission = $assignment->workSubmissions->first();
                // You might want to map these to more user-friendly strings in the view or here
                $assignment->derived_status = $latestSubmission->status;
            } else {
                $assignment->derived_status = $assignment->status; // Fallback to assignment's own status
            }
        });


        // 3. Completed Jobs
        // JobAssignments for the freelancer that are marked as 'completed' or have a final 'approved_by_client' work submission.
        $completedJobAssignments = JobAssignment::with(['job.client', 'workSubmissions' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])
            ->where('freelancer_id', $freelancerId)
            ->where(function ($query) {
                $query->where('status', 'completed')
                      ->orWhereHas('workSubmissions', function ($subQuery) {
                          $subQuery->where('status', WorkSubmission::STATUS_APPROVED_BY_CLIENT);
                      });
            })
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $completedJobAssignments->each(function ($assignment) {
            $assignment->derived_status = 'Completed'; // Or derive from submission if needed
        });

        // Counts for dashboard stats (can be refined)
        $activeAssignmentsCount = $activeJobAssignments->count(); // More accurate count of active
        $pendingProposalsCount = Proposal::where('user_id', $freelancerId)
            ->where('status', 'pending')
            ->count();
        $tasksDueSoonCount = 0; // Still a placeholder

        return view('freelancer.dashboard', compact(
            'newJobOpportunities',
            'activeJobAssignments',
            'completedJobAssignments',
            'activeAssignmentsCount',
            'pendingProposalsCount',
            'tasksDueSoonCount'
        ));
    }
}
