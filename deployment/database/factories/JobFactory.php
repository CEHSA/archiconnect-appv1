<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Admin;
use App\Models\Job; // Ensure Job model is imported for type hints if needed elsewhere
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $clientUser = User::factory()->create(['role' => User::ROLE_CLIENT]);
        
        return [
            'user_id' => $clientUser->id,
            'client_id' => $clientUser->id,
            'title' => fake()->sentence(),
            'description' => fake()->paragraphs(3, true),
            'budget' => fake()->numberBetween(1000, 10000),
            'skills_required' => fake()->words(3, true),
            'status' => fake()->randomElement(['open', 'assigned', 'in_progress', 'completed', 'cancelled']),
            'hourly_rate' => fake()->optional()->numberBetween(20, 100),
            'not_to_exceed_budget' => fake()->optional()->numberBetween(1000, 5000),
            'created_by_admin_id' => null, // Keep forced null to confirm if this is the sole FK issue source
        ];
    }

    /**
     * Indicate that the job is open.
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'open',
        ]);
    }

    /**
     * Indicate that the job is assigned.
     */
    public function assigned(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'assigned',
        ]);
    }

    /**
     * Indicate that the job is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    /**
     * Indicate that the job is completed.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    /**
     * Indicate that the job is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
