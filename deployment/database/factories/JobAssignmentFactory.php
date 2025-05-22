<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\JobAssignment>
 */
class JobAssignmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Create related models first
        $job = Job::factory()->create(); // This should create a client and set job.client_id
        $freelancer = User::factory()->create(['role' => User::ROLE_FREELANCER]);
        $admin = \App\Models\Admin::factory()->create(); // Use Admin factory

        return [
            'job_id' => $job->id, // Use the created job's ID
            'client_id' => $job->client_id, // Get client_id from the created Job
            'freelancer_id' => $freelancer->id,
            'assigned_by_admin_id' => $admin->id,
            'status' => $this->faker->randomElement(['pending', 'accepted', 'declined', 'in_progress', 'completed']),
            'freelancer_remarks' => $this->faker->optional(0.7)->paragraph(),
            'admin_remarks' => $this->faker->optional(0.5)->paragraph(),
        ];
    }

    /**
     * Indicate that the job assignment is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    /**
     * Indicate that the job assignment is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
        ]);
    }

    /**
     * Indicate that the job assignment is declined.
     */
    public function declined(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'declined',
        ]);
    }

    /**
     * Indicate that the job assignment is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    /**
     * Indicate that the job assignment is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
