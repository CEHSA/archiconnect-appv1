<?php

namespace Database\Factories;

use App\Models\JobAssignment;
use App\Models\User;
use App\Models\WorkSubmission;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkSubmission>
 */
class WorkSubmissionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_assignment_id' => JobAssignment::factory(),
            'freelancer_id' => User::factory()->freelancer()->create()->id,
            'admin_id' => fake()->optional(0.3)->randomElement([
                User::factory()->admin()->create()->id,
                null
            ]),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'file_path' => 'submissions/' . fake()->uuid() . '.pdf',
            'original_filename' => fake()->word() . '.pdf',
            'mime_type' => 'application/pdf',
            'size' => fake()->numberBetween(1000, 5000000),
            'status' => fake()->randomElement(['submitted', 'under_review', 'approved', 'rejected']),
            'submitted_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'reviewed_at' => fake()->optional(0.7)->dateTimeBetween('-2 weeks', 'now'),
            'admin_remarks' => fake()->optional(0.5)->paragraph(),
        ];
    }

    /**
     * Indicate that the submission is submitted.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'submitted',
            'reviewed_at' => null,
            'admin_remarks' => null,
        ]);
    }

    /**
     * Indicate that the submission is under review.
     */
    public function underReview(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'under_review',
            'admin_id' => User::factory()->admin()->create()->id,
            'reviewed_at' => null,
        ]);
    }

    /**
     * Indicate that the submission is approved.
     */
    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'admin_id' => User::factory()->admin()->create()->id,
            'reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'admin_remarks' => fake()->paragraph(),
        ]);
    }

    /**
     * Indicate that the submission is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'admin_id' => User::factory()->admin()->create()->id,
            'reviewed_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'admin_remarks' => fake()->paragraph(),
        ]);
    }
}
