<?php

namespace Database\Factories;

use App\Models\Job;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobAssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'job_id' => Job::factory(),
            'client_id' => User::factory()->client(),
            'freelancer_id' => User::factory()->freelancer(),
            'assigned_by_admin_id' => Admin::factory(),
            'status' => $this->faker->randomElement(['pending', 'accepted', 'declined', 'in_progress', 'completed']),
            'freelancer_remarks' => $this->faker->optional(0.7)->paragraph(),
            'admin_remarks' => $this->faker->optional(0.5)->paragraph(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'accepted',
        ]);
    }

    public function declined(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'declined',
        ]);
    }

    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'in_progress',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }
}
