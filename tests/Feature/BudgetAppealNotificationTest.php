<?php

namespace Tests\Feature;

use App\Events\BudgetAppealCreated;
use App\Models\BudgetAppeal;
use App\Models\JobAssignment;
use App\Models\User;
use App\Notifications\BudgetAppealCreatedNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BudgetAppealNotificationTest extends TestCase
{
    public function test_notification_sent_to_admins_on_budget_appeal_creation()
    {
        Notification::fake();
        
        // Create admin users
        $admin1 = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $admin2 = User::factory()->create(['role' => User::ROLE_ADMIN]);
        
        // Create test data
        $jobAssignment = JobAssignment::factory()->create();
        $budgetAppeal = BudgetAppeal::factory()->create([
            'job_assignment_id' => $jobAssignment->id,
            'requested_budget' => 1500.00,
            'reason' => 'Additional scope requirements'
        ]);

        // Trigger event
        event(new BudgetAppealCreated($budgetAppeal));

        // Assert notifications were sent to all admins
        Notification::assertSentTo(
            [$admin1, $admin2],
            BudgetAppealCreatedNotification::class,
            function ($notification) use ($budgetAppeal) {
                return $notification->budgetAppeal->id === $budgetAppeal->id;
            }
        );
    }

    public function test_notification_contains_correct_job_assignment()
    {
        Notification::fake();
        
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $jobAssignment = JobAssignment::factory()->create();
        $budgetAppeal = BudgetAppeal::factory()->create([
            'job_assignment_id' => $jobAssignment->id
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
