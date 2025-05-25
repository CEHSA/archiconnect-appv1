<?php
namespace Tests\Feature;

use App\Events\BudgetAppealCreated;
use App\Models\BudgetAppeal;
use App\Models\FreelancerProfile;
use App\Models\Job;
use App\Models\JobAssignment;
use App\Models\User;
use App\Notifications\BudgetAppealCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BudgetAppealNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_sent_to_admins_on_budget_appeal_creation()
    {
        Notification::fake();

        // Create admin users
        $admin1 = User::factory()->admin()->create();
        $admin2 = User::factory()->admin()->create();

        // Create required relationships
        $client     = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        FreelancerProfile::factory()->create(['user_id' => $freelancer->id]);

        $job = Job::factory()->create([
            'client_id' => $client->id,
            'status'    => 'open',
        ]);

        // Create job assignment with proper client and freelancer
        $jobAssignment = JobAssignment::factory()->create([
            'job_id'               => $job->id,
            'client_id'            => $client->id,
            'freelancer_id'        => $freelancer->id,
            'assigned_by_admin_id' => $admin1->id,
            'status'               => 'active',
        ]);

        // Create budget appeal
        $budgetAppeal = BudgetAppeal::factory()->create([
            'job_assignment_id' => $jobAssignment->id,
            'requested_budget'  => 1500.00,
            'reason'            => 'Additional scope requirements',
            'freelancer_id'     => $freelancer->id,
        ]);

        // Send notification to admins
        $notification = new BudgetAppealCreatedNotification($budgetAppeal, $jobAssignment);
        $admin1->notify($notification);
        $admin2->notify($notification);

        // Assert notifications were sent to all admins
        Notification::assertSentTo(
            [$admin1, $admin2],
            BudgetAppealCreatedNotification::class,
            function ($notification) use ($budgetAppeal, $jobAssignment) {
                return $notification->appeal->id === $budgetAppeal->id &&
                $notification->jobAssignment->id === $jobAssignment->id;
            }
        );
    }

    public function test_notification_contains_correct_job_assignment()
    {
        Notification::fake();

        // Create required relationships
        $admin      = User::factory()->admin()->create();
        $client     = User::factory()->client()->create();
        $freelancer = User::factory()->freelancer()->create();
        FreelancerProfile::factory()->create(['user_id' => $freelancer->id]);

        $job = Job::factory()->create([
            'client_id' => $client->id,
            'status'    => 'open',
        ]);

        // Create job assignment with proper client and freelancer
        $jobAssignment = JobAssignment::factory()->create([
            'job_id'               => $job->id,
            'client_id'            => $client->id,
            'freelancer_id'        => $freelancer->id,
            'assigned_by_admin_id' => $admin->id,
            'status'               => 'active',
        ]);

        // Create budget appeal
        $budgetAppeal = BudgetAppeal::factory()->create([
            'job_assignment_id' => $jobAssignment->id,
            'requested_budget'  => 1500.00,
            'reason'            => 'Additional scope requirements',
            'freelancer_id'     => $freelancer->id,
        ]);

        event(new BudgetAppealCreated($budgetAppeal));

        Notification::assertSentTo(
            $admin,
            BudgetAppealCreatedNotification::class,
            function ($notification) use ($jobAssignment) {
                return $notification->jobAssignment->id === $jobAssignment->id;
            }
        );
    }
}
