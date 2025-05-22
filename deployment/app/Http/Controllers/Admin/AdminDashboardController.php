<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog; // Import the AdminActivityLog model
use App\Models\Job; // Import the Job model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Later, fetch actual data: totalProjects, activeProjects, completedProjects, totalHoursLogged
        // For now, use placeholder data or zeros.
        $stats = [
            'totalProjects' => Job::count(), // Placeholder
            'activeProjects' => Job::whereIn('status', ['posted', 'pending', 'in_progress'])->count(), // Placeholder
            'completedProjects' => Job::where('status', 'completed')->count(), // Placeholder
            'totalHoursLogged' => 16.0, // Placeholder - This would likely come from time logs
        ];

        // Fetch all relevant jobs, ordered by latest
        // Using the exact statuses found in the database via the new MCP tool
        $statusesToFetch = ['open', 'approved', 'submitted', 'in_progress', 'completed'];

        $recentJobs = Job::whereIn('status', $statusesToFetch)
                            ->latest() // Orders by created_at descending by default
                            ->get();

        // Update activeProjects count to reflect actual active statuses found
        // Assuming 'open', 'approved', 'submitted', 'in_progress' are active states
        $activeStatusesForCount = ['open', 'approved', 'submitted', 'in_progress'];
        $stats['activeProjects'] = Job::whereIn('status', $activeStatusesForCount)->count();


        // Fetch recent admin activities
        $recentActivity = AdminActivityLog::with('admin') // Eager load admin details
                                          ->latest()      // Order by created_at descending
                                          ->take(10)      // Get the latest 10 activities
                                          ->get();

        return view('admin.dashboard', compact('stats', 'recentJobs', 'recentActivity'));
    }
}
