<?php

namespace Database\Factories;

use App\Models\JobAssignment;
use App\Models\User; // Ensure User model is imported
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dispute>
 */
class DisputeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $assignment = JobAssignment::factory()->create();
        
        // Ensure client and freelancer are actual User models and correctly associated
        // The JobAssignmentFactory should handle creating valid client and freelancer
        $reporter = $assignment->client;
        $reportedUser = $assignment->freelancer;

        // Fallback: if client or freelancer is not set by JobAssignmentFactory, create them.
        // This indicates a potential deeper issue in JobAssignmentFactory or its dependencies if hit often.
        if (!$reporter) {
            $reporter = User::factory()->create(['role' => User::ROLE_CLIENT]);
            $assignment->client_id = $reporter->id;
        }
        if (!$reportedUser) {
            $reportedUser = User::factory()->create(['role' => User::ROLE_FREELANCER]);
            $assignment->freelancer_id = $reportedUser->id;
        }
        // Save assignment if IDs were potentially updated directly
        if ($assignment->isDirty(['client_id', 'freelancer_id'])) {
            $assignment->save();
        }
        
        // Re-fetch to ensure relationships are loaded if they were null
        $assignment->refresh();
        $reporter = $assignment->client;
        $reportedUser = $assignment->freelancer;

        // If still null, then there's a fundamental issue with factory setup.
        // For the test to proceed, we must have valid reporter and reportedUser.
        if (!$reporter || !$reportedUser) {
            throw new \LogicException('Failed to create valid reporter/reportedUser for DisputeFactory.');
        }

        return [
            'job_assignment_id' => $assignment->id,
            'reporter_id' => $reporter->id,
            'reported_id' => $reportedUser->id, // Matches migration column name
            'reason' => $this->faker->sentence,
            'status' => $this->faker->randomElement(['open', 'under_review', 'resolved', 'closed_resolved', 'closed_unresolved']),
            'admin_remarks' => $this->faker->optional()->paragraph,
        ];
    }
}
