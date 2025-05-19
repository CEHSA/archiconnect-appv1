<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ClientProfileController;
use App\Http\Controllers\FreelancerProfileController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\JobCommentController;
use App\Http\Controllers\Admin\JobController as AdminJobController;
use App\Http\Controllers\Admin\JobAssignmentController as AdminJobAssignmentController;
use App\Http\Controllers\Admin\WorkSubmissionController as AdminWorkSubmissionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\Admin\BudgetAppealController as AdminBudgetAppealController;
use App\Http\Controllers\Admin\DisputeController as AdminDisputeController;
use App\Http\Controllers\Admin\BriefingRequestController as AdminBriefingRequestController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\AssignmentTaskController as AdminAssignmentTaskController; // Added
use App\Http\Controllers\Admin\TimeLogController as AdminTimeLogController; // Added
use App\Http\Controllers\Freelancer\JobController as FreelancerJobController;
use App\Http\Controllers\Freelancer\JobAssignmentController as FreelancerJobAssignmentController;
use App\Http\Controllers\Admin\MessageController as AdminMessageController;
use App\Http\Controllers\Freelancer\MessageController as FreelancerMessageController;
use App\Http\Controllers\Freelancer\TimeLogController as FreelancerTimeLogController;
use App\Http\Controllers\Freelancer\WorkSubmissionController as FreelancerWorkSubmissionController;
use App\Http\Controllers\Freelancer\AssignmentTaskController as FreelancerAssignmentTaskController;
use App\Http\Controllers\Client\BriefingRequestController;
use App\Http\Controllers\Client\WorkSubmissionController;
use App\Http\Controllers\Client\MessageController as ClientMessageController;
use App\Http\Controllers\Freelancer\TaskProgressController as FreelancerTaskProgressController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\AdminProfileController;
use App\Http\Controllers\Freelancer\BudgetAppealController as FreelancerBudgetAppealController;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/', function () {
    return redirect()->route('login');
});

// General dashboard route now points to DashboardController@index
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Admin Routes
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController as AdminAuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController as AdminPasswordResetLinkController;
use App\Http\Controllers\Admin\Auth\NewPasswordController as AdminNewPasswordController;
use App\Http\Controllers\Admin\AdminDashboardController;

Route::prefix('admin')->name('admin.')->group(function () {
    // Guest admin routes
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('/login', [AdminAuthenticatedSessionController::class, 'store']);

        Route::get('forgot-password', [AdminPasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [AdminPasswordResetLinkController::class, 'store'])->name('password.email');
        Route::get('reset-password/{token}', [AdminNewPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password', [AdminNewPasswordController::class, 'store'])->name('password.store');
    });

    // Authenticated admin routes
    Route::post('/logout', [AdminAuthenticatedSessionController::class, 'destroy'])->middleware('auth:admin')->name('logout');
    
    Route::middleware(['auth:admin'])->group(function () { // This is the correct group for authenticated admin routes
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        // Admin profile routes
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/information', [AdminProfileController::class, 'updateInformation'])->name('profile.information.update');
        Route::patch('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password.update');
        
        // Admin job management
        Route::resource('jobs', AdminJobController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update']);
        Route::resource('job-assignments', AdminJobAssignmentController::class);
        Route::resource('job-assignments.tasks', AdminAssignmentTaskController::class)->shallow(); // Added for admin task management
        
        // Admin user management
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);
        
        // Admin Reports Routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/job-progress', [ReportController::class, 'jobProgress'])->name('job-progress');
            Route::get('/freelancer-performance', [ReportController::class, 'freelancerPerformance'])->name('freelancer-performance');
            Route::get('/client-project-status', [ReportController::class, 'clientProjectStatus'])->name('client-project-status');
            Route::get('/financials', [ReportController::class, 'financials'])->name('financials');
            // Route::get('/generate-pdf', [ReportController::class, 'generatePdf'])->name('generate-pdf'); // Removed as method is missing
        });
        
        // Admin Messages Routes
        Route::resource('messages', AdminMessageController::class);
        Route::get('/messages/conversations/{conversation}', [AdminMessageController::class, 'showConversation'])->name('messages.showConversation');
        
        // Admin Settings Routes
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingsController::class, 'store'])->name('settings.store');
        
        // Admin Work Submissions
        Route::resource('work-submissions', AdminWorkSubmissionController::class);
        
        // Admin Budget Appeals
        Route::resource('budget-appeals', AdminBudgetAppealController::class)->except(['create', 'store']);
        
        // Admin Disputes Management
        Route::resource('disputes', AdminDisputeController::class);
        
        // Admin Briefing Requests
        Route::resource('briefing-requests', AdminBriefingRequestController::class);
        
        // Admin Payments
        Route::resource('payments', AdminPaymentController::class);

        // Admin Time Logs
        Route::get('time-logs', [AdminTimeLogController::class, 'index'])->name('time-logs.index');
        Route::get('time-logs/{timeLog}', [AdminTimeLogController::class, 'show'])->name('time-logs.show');
        Route::post('time-logs/{timeLog}/review', [AdminTimeLogController::class, 'review'])->name('time-logs.review');
        Route::get('time-logs/{timeLog}/download-proof', [AdminTimeLogController::class, 'downloadProof'])->name('time-logs.download-proof');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notification Routes (accessible to all authenticated users)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notificationId}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');

    // User Dispute Submission Routes (related to a JobAssignment)
    Route::get('/job-assignments/{jobAssignment}/disputes/create', [DisputeController::class, 'create'])->name('job_assignments.disputes.create');
    Route::post('/job-assignments/{jobAssignment}/disputes', [DisputeController::class, 'store'])->name('job_assignments.disputes.store');
    Route::get('/disputes/{dispute}', [DisputeController::class, 'show'])->name('disputes.show');

    // Client Routes
    // Note: Client and Freelancer routes are inside the main 'auth' group (default guard)
    // If they need to be standalone, they should be moved outside the main 'auth' group as well.
    // For now, assuming they use the default 'web' guard and 'auth' middleware.
    Route::middleware(['role:' . User::ROLE_CLIENT])->prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'clientDashboard'])->name('dashboard');
        // Client Profile Routes
        Route::resource('profile', ClientProfileController::class)->except(['index', 'show', 'destroy']);
        // Client Briefing Scheduling Routes
        Route::resource('briefing-requests', BriefingRequestController::class);
        // Client Work Submission Review Routes
        Route::resource('work-submissions', WorkSubmissionController::class)->only(['index', 'show']);
        // Client Job Routes
        Route::resource('jobs', JobController::class);
        // Job Proposals Routes
        Route::get('/jobs/{job}/proposals', [ProposalController::class, 'jobProposals'])
            ->name('jobs.proposals');
        Route::patch('/proposals/{proposal}/status', [ProposalController::class, 'updateStatus'])
            ->name('proposals.update-status');
        // Job Comments Routes
        Route::get('/jobs/{job}/comments', [JobCommentController::class, 'index'])->name('jobs.comments.index');
        Route::post('/jobs/{job}/comments', [JobCommentController::class, 'store'])->name('jobs.comments.store');

        // Client Message Routes
        Route::get('/messages', [ClientMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{conversation}', [ClientMessageController::class, 'show'])->name('messages.show');
        Route::post('/messages', [ClientMessageController::class, 'store'])->name('messages.store');

        // Client Disputes Routes
        Route::get('/disputes', [DisputeController::class, 'clientIndex'])->name('disputes.index');
    });

    // Freelancer Routes
    Route::middleware(['role:' . User::ROLE_FREELANCER])->prefix('freelancer')->name('freelancer.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'freelancerDashboard'])->name('dashboard');
        // Freelancer Profile Routes
        Route::resource('profile', FreelancerProfileController::class)->except(['index', 'show', 'destroy']);
        // Job Browsing & Proposal Routes
        Route::get('/jobs', [JobController::class, 'browse'])->name('jobs.browse'); // This uses the general JobController
        Route::get('/jobs/{job}', [FreelancerJobController::class, 'show'])->name('jobs.show'); // New route for specific job view
        Route::resource('proposals', ProposalController::class)->only(['index', 'store', 'show']);
        // Job Comments Routes
        Route::get('/jobs/{job}/comments', [JobCommentController::class, 'index'])->name('jobs.comments.index');
        Route::post('/comments/{comment}/discussed', [JobCommentController::class, 'markAsDiscussed'])->name('comments.discussed');
        Route::get('/comments/needs-attention', [JobCommentController::class, 'needsAttention'])->name('comments.needs-attention');

        // Freelancer Job Assignments Routes
        Route::get('/assignments', [FreelancerJobAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [FreelancerJobAssignmentController::class, 'show'])->name('assignments.show');
        Route::patch('/assignments/{assignment}/status', [FreelancerJobAssignmentController::class, 'updateStatus'])->name('assignments.update-status');

        // Freelancer Message Routes
        Route::get('/messages', [FreelancerMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/create-admin', [FreelancerMessageController::class, 'createAdminMessage'])->name('messages.createAdmin'); // New route for admin message
        Route::get('/messages/{conversation}', [FreelancerMessageController::class, 'show'])->name('messages.show');
        Route::get('/assignments/{assignment}/messages/create', [FreelancerMessageController::class, 'create'])->name('assignments.messages.create'); // For assignment-specific messages
        Route::post('/messages', [FreelancerMessageController::class, 'store'])->name('messages.store');

        // Freelancer Time Log Routes
        Route::get('/time-logs', [FreelancerTimeLogController::class, 'index'])->name('time-logs.index');
        Route::post('/assignment-tasks/{task}/time-logs/start', [FreelancerTimeLogController::class, 'startTimer'])->name('assignment-tasks.time-logs.start');
        Route::post('/time-logs/{timeLog}/stop', [FreelancerTimeLogController::class, 'stopTimer'])->name('time-logs.stop');
        Route::patch('/time-logs/{timeLog}', [FreelancerTimeLogController::class, 'updateLog'])->name('time-logs.update');
        Route::delete('/time-logs/{timeLog}', [FreelancerTimeLogController::class, 'destroy'])->name('time-logs.destroy');


        // Freelancer Work Submission Routes
        Route::get('/assignments/{assignment}/submissions/create', [FreelancerWorkSubmissionController::class, 'create'])->name('assignments.submissions.create');
        Route::post('/assignments/{assignment}/submissions', [FreelancerWorkSubmissionController::class, 'store'])->name('assignments.submissions.store');
        // Potentially add index/show for freelancer to view their own submissions later if needed
        // Route::get('/assignments/{assignment}/submissions', [FreelancerWorkSubmissionController::class, 'index'])->name('assignments.submissions.index');
        // Route::get('/submissions/{submission}', [FreelancerWorkSubmissionController::class, 'show'])->name('submissions.show');

        // Freelancer Task Progress Routes (nested under assignments)
        Route::get('/assignments/{assignment}/progress/create', [FreelancerTaskProgressController::class, 'create'])->name('assignments.progress.create');
        Route::post('/assignments/{assignment}/progress', [FreelancerTaskProgressController::class, 'store'])->name('assignments.progress.store');
        Route::get('/task-progress/{taskProgress}/download', [FreelancerTaskProgressController::class, 'download'])->name('task-progress.download');

        // Freelancer Budget Appeal Routes (nested under assignments)
        Route::resource('assignments.budget-appeals', FreelancerBudgetAppealController::class)->only(['create', 'store'])->shallow();

        // Freelancer Assignment Tasks Routes (nested under assignments)
        Route::resource('assignments.tasks', FreelancerAssignmentTaskController::class)->shallow();

        // Freelancer Disputes Routes
        Route::get('/disputes', [DisputeController::class, 'freelancerIndex'])->name('disputes.index');
        // Note: Dispute creation is already handled by job_assignments.disputes.create and .store

    });
});


require __DIR__.'/auth.php';
