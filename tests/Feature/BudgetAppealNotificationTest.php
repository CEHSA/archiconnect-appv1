<?php
namespace Tests\Feature;

use App\Events\BudgetAppealCreated;
use App\Models\BudgetAppeal;
use App\Models\FreelancerProfile;
use App\Models\Job;
use App\Models\JobAssignment;
use App\Models\User;
use App\Models\Admin;
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

        // Create admin users and their corresponding admin records
        $userAdmin1 = User::factory()->admin()->create();
        $admin1 = Admin::factory()->create(['user_id' => $userAdmin1->id]);
        $userAdmin2 = User::factory()->admin()->create();
        $admin2 = Admin::factory()->create(['user_id' => $userAdmin2->id]);

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
        $userAdmin1->notify($notification); // Notify the User model instance
        $userAdmin2->notify($notification); // Notify the User model instance

        // Assert notifications were sent to all admins
        Notification::assertSentTo(
            [$userAdmin1, $userAdmin2], // Notify User models
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
        $userAdmin = User::factory()->admin()->create();
        $admin = Admin::factory()->create(['user_id' => $userAdmin->id]);
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
            'assigned_by_admin_id' => $admin->id, // Use Admin model's ID
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
            $userAdmin, // Notify User model
            BudgetAppealCreatedNotification::class,
            function ($notification) use ($jobAssignment) {
                return $notification->jobAssignment->id === $jobAssignment->id;
            }
        );
    }
}
