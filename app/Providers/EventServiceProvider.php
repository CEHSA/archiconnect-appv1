<?php

namespace App\Providers;

use App\Events\JobAssigned;
use App\Listeners\SendFreelancerAssignmentNotification;
use App\Events\MessageApprovedByAdmin;
use App\Listeners\NotifyParticipantsOfApprovedMessage; // Changed from NotifyClientOfApprovedMessage
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

// Added for Job Acceptance Flow
use App\Events\JobAcceptanceRequested;
use App\Listeners\NotifyAdminOfJobAcceptanceRequest;
use App\Events\JobPostedToFreelancers; // Added for new event
use App\Listeners\NotifyFreelancersOfPostedJob; // Added for new listener
use App\Events\MessageReviewedByAdmin; // Added for new event
use App\Events\MessageRejectedByAdmin; // Added for rejected message event
use App\Listeners\NotifySenderOfRejectedMessage; // Added for rejected message listener

// Added for Job Application Flow
use App\Events\JobApplicationSubmitted;
use App\Listeners\NotifyAdminOfJobApplication;
use App\Events\JobApplicationStatusUpdated; // Added
use App\Listeners\NotifyFreelancerOfApplicationStatusUpdate; // Added
use App\Events\ClientMessageSent; // Added
use App\Listeners\NotifyParticipantsOfClientMessage; // Added
use App\Events\AdminMessageSent; // Added
use App\Listeners\NotifyParticipantsOfAdminMessage; // Added

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
        \Illuminate\Auth\Events\Login::class => [
            \App\Listeners\LogSuccessfulLogin::class,
        ],
        \Illuminate\Auth\Events\Logout::class => [
            \App\Listeners\LogSuccessfulLogout::class,
        ],
        UserCreatedByAdmin::class => [
            LogAdminActivity::class,
        ],
        \App\Events\JobCommentStatusUpdated::class => [
            NotifyUsersOfJobCommentStatusUpdate::class,
        ],
        \App\Events\JobCommentCreated::class => [
            NotifyUsersOfNewJobComment::class,
            NotifyAdminAndFreelancerOfClientComment::class,
        ],
        \App\Events\BriefingRequestCreated::class => [
            NotifyAdminsOfNewBriefingRequest::class,
        ],
        JobAssigned::class => [
            SendFreelancerAssignmentNotification::class,
        ],
        \App\Events\FreelancerRespondedToAssignment::class => [
            \App\Listeners\NotifyAdminOfFreelancerResponse::class,
        ],
        \App\Events\FreelancerMessageCreated::class => [
            \App\Listeners\NotifyAdminsOfPendingMessage::class,
        ],
        MessageApprovedByAdmin::class => [
            NotifyParticipantsOfApprovedMessage::class,
        ],
        AdminJobPosted::class => [
            NotifyFreelancersAboutNewJob::class,
            LogAdminJobCreation::class,
        ],
        FreelancerWorkSubmitted::class => [
            NotifyAdminOfWorkSubmission::class,
        ],
        WorkSubmissionReviewedByAdmin::class => [
            NotifyFreelancerOfSubmissionReview::class,
        ],
        \App\Events\BudgetAppealForwardedToClient::class => [
            \App\Listeners\NotifyClientOfBudgetAppeal::class,
        ],
        \App\Events\BudgetAppealDecisionMade::class => [
            \App\Listeners\NotifyFreelancerOfBudgetAppealDecision::class,
        ],
        \App\Events\BudgetAppealCreated::class => [
            \App\Listeners\NotifyAdminsOfNewBudgetAppeal::class,
        ],
        ClientWorkSubmissionReviewed::class => [
            NotifyAdminsOfClientWorkSubmissionReview::class,
        ],
        FreelancerTaskProgressSubmitted::class => [
            NotifyAdminsOfFreelancerTaskProgress::class,
        ],
        JobCompleted::class => [
            NotifyUsersOfJobCompletion::class,
        ],
        \App\Events\PaymentProcessed::class => [
            \App\Listeners\NotifyFreelancerOfPayment::class,
        ],
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
        JobAcceptanceRequested::class => [
            NotifyAdminOfJobAcceptanceRequest::class,
        ],
        JobPostedToFreelancers::class => [
            NotifyFreelancersOfPostedJob::class,
        ],
        MessageReviewedByAdmin::class => [
            LogAdminActivity::class,
        ],
        MessageRejectedByAdmin::class => [
            NotifySenderOfRejectedMessage::class,
        ],
        JobApplicationSubmitted::class => [
            NotifyAdminOfJobApplication::class,
        ],
        JobApplicationStatusUpdated::class => [
            NotifyFreelancerOfApplicationStatusUpdate::class,
        ],
        ClientMessageSent::class => [
            NotifyParticipantsOfClientMessage::class,
        ],
        AdminMessageSent::class => [
            NotifyParticipantsOfAdminMessage::class,
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
