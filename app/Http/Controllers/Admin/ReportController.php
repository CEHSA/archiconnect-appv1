<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobAssignment;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Display the main reports index page.
     */
    public function index()
    {
        // In a real application, you would fetch recent report activity data
        // For now, we'll just return the view
        return view('admin.reports.index');
    }

    /**
     * Display a listing of job progress reports.
     */
    public function jobProgress(Request $request)
    {
        $assignments = JobAssignment::with(['job', 'freelancer', 'taskProgress', 'workSubmissions'])
            ->latest()
            ->get();

        return view('admin.reports.job-progress', compact('assignments'));
    }

    /**
     * Display a listing of freelancer performance reports.
     */
    public function freelancerPerformance(Request $request)
    {
        $freelancers = \App\Models\FreelancerProfile::with(['user', 'jobAssignments.job', 'jobAssignments.timeLogs', 'jobAssignments.workSubmissions'])
            ->get();

        return view('admin.reports.freelancer-performance', compact('freelancers'));
    }

    /**
     * Display a listing of client project status reports.
    /**
     * Display a listing of client project status reports.
     */
    public function clientProjectStatus(Request $request)
    {
        // Fetch clients and eager load their relationships to get project-related data
        $clients = \App\Models\User::where('role', \App\Models\User::ROLE_CLIENT)
            ->with([
                'jobs.assignments.freelancer', // Corrected: removed trailing '.user' as 'freelancer' relation on JobAssignment already returns a User model
                'briefingRequests', // Briefing requests submitted by client
            ])
            ->get();

        // You might want to add filtering or sorting based on request parameters here later

        return view('admin.reports.client-project-status', compact('clients'));
    }

    /**
     * Display a listing of financial reports.
    /**
     * Display a listing of financial reports.
     */
    public function financials(Request $request)
    {
        // Fetch completed job assignments with related data for potential earnings calculation
        $completedAssignments = JobAssignment::where('status', 'completed') // Assuming 'completed' status indicates readiness for payment/final earnings
            ->with(['job.user', 'freelancer.user', 'timeLogs'])
            ->get();

        // Calculate total approved hours and estimated earnings from completed assignments
        $totalEstimatedEarnings = 0;
        $completedAssignmentsData = $completedAssignments->map(function ($assignment) use (&$totalEstimatedEarnings) {
            $totalApprovedHours = $assignment->timeLogs->sum('duration') / 3600; // Sum duration in hours
            $estimatedEarnings = $totalApprovedHours * ($assignment->job->hourly_rate ?? 0); // Use job's hourly rate

            // Cap earnings at the job's budget if a not-to-exceed budget exists
            if ($assignment->job->budget !== null && $estimatedEarnings > $assignment->job->budget) {
                 $estimatedEarnings = $assignment->job->budget;
            }

            $totalEstimatedEarnings += $estimatedEarnings;

            return [
                'assignment_id' => $assignment->id,
                'job_title' => $assignment->job->title ?? 'N/A',
                'client_name' => $assignment->job->user->name ?? 'N/A',
                'freelancer_name' => $assignment->freelancer->user->name ?? 'N/A',
                'total_approved_hours' => round($totalApprovedHours, 2),
                'estimated_earnings' => round($estimatedEarnings, 2),
                'job_budget' => $assignment->job->budget,
                'job_hourly_rate' => $assignment->job->hourly_rate,
            ];
        });

        // Fetch all payment records
        $payments = \App\Models\Payment::with(['jobAssignment.job', 'freelancer']) // Corrected: freelancer.user to freelancer
            ->latest()
            ->get();

        $totalPayouts = $payments->sum('amount');

        $financialData = [
            'totalEstimatedEarningsFromCompletedAssignments' => round($totalEstimatedEarnings, 2),
            'totalPayouts' => round($totalPayouts, 2),
            'completedAssignments' => $completedAssignmentsData,
            'payments' => $payments,
        ];

        return view('admin.reports.financials', compact('financialData'));
    }
}
