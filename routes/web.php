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
use App\Http\Controllers\Freelancer\JobApplicationController as FreelancerJobApplicationController; // Added
use Illuminate\Support\Facades\Route;
use App\Models\User;

// Temporary diagnostic routes - REMOVE AFTER USE
Route::get('/diagnose-user-class', function () {
    try {
        $output = [];

        // 1. Basic class checks
        $output[] = "1. Class name check:";
        $output[] = "User::class = " . User::class;
        $output[] = "class_exists(): " . (class_exists(User::class) ? 'true' : 'false');
        $output[] = "";

        // 2. Get declared classes to verify loading
        $output[] = "2. Is User in declared classes?";
        $declaredClasses = get_declared_classes();
        $userClass = array_filter($declaredClasses, fn($class) => str_contains($class, 'User'));
        $output[] = "Found User classes: " . implode(", ", $userClass);
        $output[] = "";

        // 3. Check constants using different methods
        $output[] = "3. Constants check:";
        try {
            $output[] = "All declared constants in User class:";
            $reflect = new ReflectionClass(User::class);
            $constants = $reflect->getConstants();
            $output[] = "ReflectionClass::getConstants(): " . json_encode($constants, JSON_PRETTY_PRINT);

            $output[] = "\nTrying direct ROLES constant access:";
            $output[] = "defined('ROLES'): " . (defined('ROLES') ? 'true' : 'false');
            $output[] = "defined('User::ROLES'): " . (defined('User::ROLES') ? 'true' : 'false');
            $output[] = "defined('\\App\\Models\\User::ROLES'): " . (defined('\\App\\Models\\User::ROLES') ? 'true' : 'false');

            // Try to directly reference it (will throw error if undefined)
            try {
                $roles = User::ROLES;
                $output[] = "Direct access User::ROLES succeeded. Value: " . json_encode($roles);
            } catch (\Error $e) {
                $output[] = "Direct access User::ROLES failed: " . $e->getMessage();
            }
        } catch (\Exception $e) {
            $output[] = "Error during constants check: " . $e->getMessage();
        }
        $output[] = "";

        // 4. Try creating an instance
        $output[] = "4. Instance creation:";
        $user = new User();
        $output[] = "Successfully created User instance";
        $output[] = "get_class(): " . get_class($user);
        $output[] = "";

        // 5. Reflection information
        $output[] = "5. Reflection info:";
        $reflect = new ReflectionClass(User::class);
        $output[] = "Constants: " . json_encode($reflect->getConstants());
        $output[] = "Namespace: " . $reflect->getNamespaceName();
        $output[] = "Location: " . $reflect->getFileName();
        $output[] = "";

        // 6. Try accessing constant through instance (though not standard practice)
        $output[] = "6. Alternative constant access attempts:";
        $output[] = "defined('App\\Models\\User::ROLES'): " . (defined('App\\Models\\User::ROLES') ? 'true' : 'false');

        // Return results as formatted HTML
        return "<pre>" . implode("\n", $output) . "</pre>";
    } catch (\Throwable $e) {
        return "Error in diagnostic route: " . $e->getMessage() . "\n" . $e->getTraceAsString();
    }
});

Route::get('/diagnose-user-file', function () {
    try {
        $output = [];

        // 1. Get User.php file details
        $userFile = app_path('Models/User.php');
        $output[] = "1. File Information:";
        $output[] = "Path: " . $userFile;
        $output[] = "Exists: " . (file_exists($userFile) ? 'Yes' : 'No');
        $output[] = "Size: " . filesize($userFile) . " bytes";
        $output[] = "Last Modified: " . date("Y-m-d H:i:s", filemtime($userFile));
        $output[] = "Permissions: " . decoct(fileperms($userFile) & 0777);
        $output[] = "";

        // 2. Read file content
        $output[] = "2. File Content Analysis:";
        $content = file_get_contents($userFile);
        if ($content === false) {
            $output[] = "Failed to read file content";
        } else {
            // Look for ROLES constant definition
            if (preg_match('/public const ROLES = \[(.*?)\];/s', $content, $matches)) {
                $output[] = "Found ROLES constant definition:";
                $output[] = $matches[0];

                // Get some context around the match
                $pos = strpos($content, $matches[0]);
                $start = max(0, $pos - 100);
                $length = strlen($matches[0]) + 200;
                $context = substr($content, $start, $length);

                $output[] = "\nContext around ROLES definition:";
                $output[] = "..." . htmlspecialchars($context) . "...";

                // Check for invisible characters
                $output[] = "\nHex dump of ROLES definition:";
                $output[] = chunk_split(bin2hex($matches[0]), 2, ' ');
            } else {
                $output[] = "ROLES constant definition not found in file content!";
            }
        }

        // 3. Compare with reflection
        $output[] = "\n3. Reflection vs File Content Comparison:";
        $reflect = new ReflectionClass(User::class);
        $output[] = "Constants from Reflection: " . json_encode($reflect->getConstants(), JSON_PRETTY_PRINT);
        $output[] = "File Location from Reflection: " . $reflect->getFileName();

        return "<pre>" . implode("\n", $output) . "</pre>";
    } catch (\Throwable $e) {
        return "Error in diagnostic route: " . $e->getMessage() . "\n" . $e->getTraceAsString();
    }
});

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

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        // Admin profile routes
        Route::get('/profile', [AdminProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile/information', [AdminProfileController::class, 'updateInformation'])->name('profile.information.update');
        Route::patch('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password.update');

        // Admin job management
        Route::get('jobs/{job}/post-to-freelancers', [AdminJobController::class, 'postToFreelancers'])->name('jobs.post-to-freelancers');
        Route::post('jobs/{job}/send-postings', [AdminJobController::class, 'sendPostings'])->name('jobs.send-postings');
        Route::resource('jobs', AdminJobController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']); // Added 'destroy'
        Route::post('jobs/{job}/comments', [JobCommentController::class, 'store'])->name('jobs.comments.store'); // Added for admin job comments
        Route::post('job-assignments/{jobAssignment}/notes', [AdminJobAssignmentController::class, 'storeNote'])->name('job-assignments.notes.store'); // Added for assignment notes
        Route::resource('job-assignments', AdminJobAssignmentController::class);
        Route::resource('job-assignments.tasks', AdminAssignmentTaskController::class)->shallow();
        Route::get('jobs/current', [AdminJobController::class, 'currentJobs'])->name('jobs.current'); // New route for current jobs

        // Admin user management
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        // Admin Reports Routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/job-progress', [ReportController::class, 'jobProgress'])->name('job-progress');
            Route::get('/freelancer-performance', [ReportController::class, 'freelancerPerformance'])->name('freelancer-performance');
            Route::get('/client-project-status', [ReportController::class, 'clientProjectStatus'])->name('client-project-status');
            Route::get('/financials', [ReportController::class, 'financials'])->name('financials');
        });

        // Admin Messages Routes
        Route::get('/messages/history', [AdminMessageController::class, 'history'])->name('messages.history'); // New route for history
        Route::resource('messages', AdminMessageController::class);
        Route::get('/messages/conversations/{conversation}', [AdminMessageController::class, 'showConversation'])->name('messages.showConversation');

        // Admin Settings Routes
        Route::get('/settings', [AdminSettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AdminSettingsController::class, 'store'])->name('settings.store');

        // Admin Work Submissions
        Route::get('work-submissions/{submission}/download', [AdminWorkSubmissionController::class, 'download'])->name('work-submissions.download');
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

        // Admin Job Applications Management
        Route::get('job-applications', [AdminJobApplicationController::class, 'index'])->name('job-applications.index'); // Assuming a new AdminJobApplicationController
        Route::get('job-applications/{application}', [AdminJobApplicationController::class, 'show'])->name('job-applications.show');
        Route::patch('job-applications/{application}/status', [AdminJobApplicationController::class, 'updateStatus'])->name('job-applications.updateStatus');
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
    Route::middleware(['role:' . User::ROLE_CLIENT])->prefix('client')->name('client.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'clientDashboard'])->name('dashboard');
        Route::resource('profile', ClientProfileController::class)->except(['index', 'show', 'destroy']);
        Route::resource('briefing-requests', BriefingRequestController::class);
        Route::get('work-submissions/{workSubmission}/download', [WorkSubmissionController::class, 'download'])->name('work-submissions.download'); // Added
        Route::resource('work-submissions', WorkSubmissionController::class)->only(['index', 'show', 'update']); // Added 'update' as it exists
        Route::resource('jobs', JobController::class);
        Route::get('/jobs/{job}/proposals', [ProposalController::class, 'jobProposals'])->name('jobs.proposals');
        Route::patch('/proposals/{proposal}/status', [ProposalController::class, 'updateStatus'])->name('proposals.update-status');
        Route::get('/jobs/{job}/comments', [JobCommentController::class, 'index'])->name('jobs.comments.index');
        Route::post('/jobs/{job}/comments', [JobCommentController::class, 'store'])->name('jobs.comments.store');
        Route::get('/messages', [ClientMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{conversation}', [ClientMessageController::class, 'show'])->name('messages.show');
        Route::post('/messages', [ClientMessageController::class, 'store'])->name('messages.store');
        Route::get('/disputes', [DisputeController::class, 'clientIndex'])->name('disputes.index');
    });

    // Freelancer Routes
    Route::middleware(['role:' . User::ROLE_FREELANCER])->prefix('freelancer')->name('freelancer.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'freelancerDashboard'])->name('dashboard');
        Route::resource('profile', FreelancerProfileController::class)->except(['index', 'show', 'destroy']);
        Route::get('/jobs', [JobController::class, 'browse'])->name('jobs.browse');
        Route::get('/posted-jobs', [FreelancerJobController::class, 'postedJobsIndex'])->name('posted-jobs.index'); // New route for posted jobs
        Route::get('/jobs/{job}', [FreelancerJobController::class, 'show'])->name('jobs.show');
        Route::post('/jobs/{job}/accept', [FreelancerJobController::class, 'acceptJob'])->name('jobs.accept'); // New route for accepting a job
        Route::get('/jobs/{job}/message-admin/create', [FreelancerMessageController::class, 'createAdminMessageForJob'])->name('jobs.message-admin.create'); // Message admin about job
        Route::post('/jobs/{job}/message-admin', [FreelancerMessageController::class, 'storeAdminMessageForJob'])->name('jobs.message-admin.store'); // Send message to admin about job
        
        // Freelancer Job Applications
        Route::get('/job-applications/create/{job_posting_id}', [FreelancerJobApplicationController::class, 'create'])->name('job-applications.create');
        Route::post('/job-applications', [FreelancerJobApplicationController::class, 'store'])->name('job-applications.store');
        // Route::get('/job-applications/{application}', [FreelancerJobApplicationController::class, 'show'])->name('job-applications.show'); // Optional: for viewing submitted application

        Route::resource('proposals', ProposalController::class)->only(['index', 'store', 'show']);
        Route::get('/jobs/{job}/comments', [JobCommentController::class, 'index'])->name('jobs.comments.index');
        Route::post('/comments/{comment}/discussed', [JobCommentController::class, 'markAsDiscussed'])->name('comments.discussed');
        Route::get('/comments/needs-attention', [JobCommentController::class, 'needsAttention'])->name('comments.needs-attention');
        Route::get('/assignments', [FreelancerJobAssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [FreelancerJobAssignmentController::class, 'show'])->name('assignments.show');
        Route::patch('/assignments/{assignment}/status', [FreelancerJobAssignmentController::class, 'updateStatus'])->name('assignments.update-status');
        Route::get('/messages', [FreelancerMessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/create-admin', [FreelancerMessageController::class, 'createAdminMessage'])->name('messages.createAdmin');
        Route::get('/messages/{conversation}', [FreelancerMessageController::class, 'show'])->name('messages.show');
        Route::get('/assignments/{assignment}/messages/create', [FreelancerMessageController::class, 'create'])->name('assignments.messages.create');
        Route::post('/messages', [FreelancerMessageController::class, 'store'])->name('messages.store');
        Route::get('/time-logs', [FreelancerTimeLogController::class, 'index'])->name('time-logs.index');
        Route::post('/assignment-tasks/{task}/time-logs/start', [FreelancerTimeLogController::class, 'startTimer'])->name('assignment-tasks.time-logs.start');
        Route::post('/time-logs/{timeLog}/stop', [FreelancerTimeLogController::class, 'stopTimer'])->name('time-logs.stop');
        Route::patch('/time-logs/{timeLog}', [FreelancerTimeLogController::class, 'updateLog'])->name('time-logs.update');
        Route::delete('/time-logs/{timeLog}', [FreelancerTimeLogController::class, 'destroy'])->name('time-logs.destroy');
        Route::get('/assignments/{assignment}/submissions/create', [FreelancerWorkSubmissionController::class, 'create'])->name('assignments.submissions.create');
        Route::post('/assignments/{assignment}/submissions', [FreelancerWorkSubmissionController::class, 'store'])->name('assignments.submissions.store');
        Route::get('/assignments/{assignment}/progress/create', [FreelancerTaskProgressController::class, 'create'])->name('assignments.progress.create');
        Route::post('/assignments/{assignment}/progress', [FreelancerTaskProgressController::class, 'store'])->name('assignments.progress.store');
        Route::get('/task-progress/{taskProgress}/download', [FreelancerTaskProgressController::class, 'download'])->name('task-progress.download');
        Route::resource('assignments.budget-appeals', FreelancerBudgetAppealController::class)->only(['create', 'store'])->shallow();
        Route::resource('assignments.tasks', FreelancerAssignmentTaskController::class)->shallow();
        Route::get('/disputes', [DisputeController::class, 'freelancerIndex'])->name('disputes.index');
    });
});

require __DIR__.'/auth.php';
