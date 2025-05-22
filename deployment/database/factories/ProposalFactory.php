<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Proposal>
 */
class ProposalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'job_id' => Job::factory(),
            'user_id' => User::factory()->create(['role' => User::ROLE_FREELANCER])->id,
            'cover_letter' => fake()->paragraphs(3, true),
            'proposed_budget' => fake()->numberBetween(500, 5000),
            'status' => fake()->randomElement(['pending', 'accepted', 'rejected']),
            'admin_remarks' => fake()->optional(0.4)->paragraph(),
        ];
    }

    /**
     * Indicate that the proposal is pending.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'admin_remarks' => null,
        ]);
    }

    /**
     * Indicate that the proposal is accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
            'admin_remarks' => fake()->paragraph(),
        ]);
    }

    /**
     * Indicate that the proposal is rejected.
     */
    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'admin_remarks' => fake()->paragraph(),
        ]);
    }
}
