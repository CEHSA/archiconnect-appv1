<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Later, fetch actual data: totalProjects, activeProjects, completedProjects, totalHoursLogged
        // For now, use placeholder data or zeros.
        $stats = [
            'totalProjects' => 3, // Placeholder
            'activeProjects' => 1, // Placeholder
            'completedProjects' => 0, // Placeholder
            'totalHoursLogged' => 16.0, // Placeholder
        ];

        // Placeholder for recent jobs and activity
        $recentJobs = [
            ['name' => 'Modern Residential House Design', 'status' => 'in progress', 'date' => '06/10/2023'],
            ['name' => 'Commercial Office Renovation', 'status' => 'open', 'date' => '06/15/2023'],
            ['name' => 'Restaurant Interior Design', 'status' => 'draft', 'date' => '06/20/2023'],
        ];
        $recentActivity = [
            ['name' => 'Site Analysis', 'status' => 'Task completed', 'date' => '06/15/2023'],
        ];

        return view('admin.dashboard', compact('stats', 'recentJobs', 'recentActivity'));
    }
}
