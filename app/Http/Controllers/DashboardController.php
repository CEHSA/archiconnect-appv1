<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Job;
use App\Models\BriefingRequest;
use App\Models\WorkSubmission;
use App\Models\JobAssignment; // Added for Freelancer Dashboard
use App\Models\Proposal; // Added for Freelancer Dashboard

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

        $activeAssignmentsCount = JobAssignment::where('freelancer_id', $freelancer->id)
            ->where('status', 'in_progress') // Assuming 'in_progress' is a valid status for active assignments
            ->count();

        $pendingProposalsCount = Proposal::where('user_id', $freelancer->id)
            ->where('status', 'pending') // Assuming 'pending' is a status for submitted but not yet accepted/rejected proposals
            ->count();

        // Placeholder - task due soon logic needs more definition (e.g., a 'due_date' on tasks or milestones within JobAssignment)
        // For now, we'll keep this as a placeholder or a count of active assignments if tasks are not granularly tracked with due dates.
        // Let's use active assignments as a proxy for "tasks" for now, or set to 0 if too abstract.
        $tasksDueSoonCount = 0; // Keeping as 0 as "tasks due soon" is not clearly defined with current models.

        $activeFreelancerJobAssignments = JobAssignment::with(['job.client', 'job']) // Eager load job and client info
            ->where('freelancer_id', $freelancer->id)
            ->where('status', 'in_progress') // Or other relevant active statuses
            ->orderBy('updated_at', 'desc') // Or by a deadline if available
            ->take(5) // Limit to a few recent active jobs for the dashboard
            ->get();

        return view('freelancer.dashboard', compact(
            'activeAssignmentsCount',
            'pendingProposalsCount',
            'tasksDueSoonCount',
            'activeFreelancerJobAssignments'
        ));
    }
}
