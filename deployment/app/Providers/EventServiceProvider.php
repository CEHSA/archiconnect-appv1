<?php

namespace App\Providers;

use App\Events\JobAssigned;
use App\Listeners\SendFreelancerAssignmentNotification;
use App\Events\MessageApprovedByAdmin; // Added
use App\Listeners\NotifyClientOfApprovedMessage; // Added
use App\Events\AdminJobPosted; // New
use App\Listeners\NotifyFreelancersAboutNewJob; // New
use App\Listeners\LogAdminJobCreation; // Added new listener
use App\Events\FreelancerWorkSubmitted; // New
use App\Listeners\NotifyAdminOfWorkSubmission; // New
use App\Events\WorkSubmissionReviewedByAdmin; // New
use App\Listeners\NotifyFreelancerOfSubmissionReview; // New
use App\Events\ClientWorkSubmissionReviewed; // Add this line
use App\Listeners\NotifyAdminsOfClientWorkSubmissionReview; // Add this line
use App\Events\FreelancerTaskProgressSubmitted; // Add this line
use App\Listeners\NotifyAdminsOfFreelancerTaskProgress; // Add this line
use App\Events\JobCompleted; // Add this line
use App\Listeners\NotifyUsersOfJobCompletion; // Add this line
use App\Listeners\NotifyAdminsOfNewBriefingRequest; // Add this line
use App\Listeners\NotifyUsersOfNewJobComment; // Add this line
use App\Listeners\NotifyUsersOfJobCommentStatusUpdate; // Add this line
use App\Listeners\NotifyAdminAndFreelancerOfClientComment; // Added for client comments on submissions

use App\Events\UserCreatedByAdmin; // New event for admin activity logging
use App\Listeners\LogAdminActivity; // New listener for admin activity

use App\Events\DisputeCreated;
use App\Listeners\NotifyAdminOfNewDispute;
use App\Listeners\NotifyReportedUserOfDispute;
use App\Events\DisputeUpdatedByAdmin;
use App\Listeners\NotifyPartiesOfDisputeUpdate;

use App\Events\FreelancerTimeLogStarted;
use App\Listeners\NotifyAdminOfTimeLogStart;
use App\Events\FreelancerTimeLogStopped;
use App\Listeners\NotifyAdminOfTimeLogStop;
use App\Events\TimeLogReviewedByAdmin;
use App\Listeners\NotifyFreelancerOfTimeLogReview;
use App\Events\ClientNotificationForApprovedTimeLog;
use App\Listeners\NotifyClientOfApprovedTimeLog;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        UserCreatedByAdmin::class => [
            LogAdminActivity::class,
        ],
        \App\Events\JobCommentStatusUpdated::class => [ // Add this line
            NotifyUsersOfJobCommentStatusUpdate::class, // Add this line
        ], // Add this line
        \App\Events\JobCommentCreated::class => [ // Add this line
            NotifyUsersOfNewJobComment::class, // Add this line
            NotifyAdminAndFreelancerOfClientComment::class, // Added for client comments on submissions
        ], // Add this line
        \App\Events\BriefingRequestCreated::class => [ // Add this line
            NotifyAdminsOfNewBriefingRequest::class, // Add this line
        ], // Add this line
        JobAssigned::class => [
            SendFreelancerAssignmentNotification::class,
        ],
        \App\Events\FreelancerRespondedToAssignment::class => [
            \App\Listeners\NotifyAdminOfFreelancerResponse::class,
        ],
        \App\Events\FreelancerMessageCreated::class => [
            \App\Listeners\NotifyAdminsOfPendingMessage::class,
        ],
        MessageApprovedByAdmin::class => [ // Added
            NotifyClientOfApprovedMessage::class, // Added
        ], // Added
        AdminJobPosted::class => [
            NotifyFreelancersAboutNewJob::class,
            LogAdminJobCreation::class, // Changed to new dedicated listener
        ],
        FreelancerWorkSubmitted::class => [ // New
            NotifyAdminOfWorkSubmission::class, // New
        ], // New
        WorkSubmissionReviewedByAdmin::class => [ // New
            NotifyFreelancerOfSubmissionReview::class, // New
        ], // New
        \App\Events\BudgetAppealForwardedToClient::class => [ // New
            \App\Listeners\NotifyClientOfBudgetAppeal::class, // New
        ], // New
        \App\Events\BudgetAppealDecisionMade::class => [ // New
            \App\Listeners\NotifyFreelancerOfBudgetAppealDecision::class, // New
        ], // New
        \App\Events\BudgetAppealCreated::class => [ // New
            \App\Listeners\NotifyAdminsOfNewBudgetAppeal::class, // New
        ], // New
        ClientWorkSubmissionReviewed::class => [ // Add this line
            NotifyAdminsOfClientWorkSubmissionReview::class, // Add this line
        ], // Add this line
        FreelancerTaskProgressSubmitted::class => [ // Add this line
            NotifyAdminsOfFreelancerTaskProgress::class, // Add this line
        ], // Add this line
        JobCompleted::class => [ // Add this line
            NotifyUsersOfJobCompletion::class, // Add this line
        ], // Add this line
        \App\Events\PaymentProcessed::class => [ // Add this line
            \App\Listeners\NotifyFreelancerOfPayment::class, // Add this line
        ], // Add this line

        DisputeCreated::class => [
            NotifyAdminOfNewDispute::class,
            NotifyReportedUserOfDispute::class,
        ],
        DisputeUpdatedByAdmin::class => [
            NotifyPartiesOfDisputeUpdate::class,
        ],

        FreelancerTimeLogStarted::class => [
            NotifyAdminOfTimeLogStart::class,
        ],
        FreelancerTimeLogStopped::class => [
            NotifyAdminOfTimeLogStop::class,
        ],
        TimeLogReviewedByAdmin::class => [
            NotifyFreelancerOfTimeLogReview::class,
        ],
        ClientNotificationForApprovedTimeLog::class => [
            NotifyClientOfApprovedTimeLog::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false; // Set to false if explicitly defining listeners here
    }
}
