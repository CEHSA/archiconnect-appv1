<?php

namespace Database\Factories;

use App\Models\JobAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TimeLog>
 */
class TimeLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('-1 month', 'now');
        $endTime = (clone $startTime)->modify('+' . rand(1, 8) . ' hours');
        $duration = $endTime->getTimestamp() - $startTime->getTimestamp();

        return [
            'job_assignment_id' => JobAssignment::factory(),
            'freelancer_id' => User::factory()->create(['role' => User::ROLE_FREELANCER])->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => $duration,
            'task_description' => fake()->sentence(),
            'is_auto_stopped' => fake()->boolean(20),
        ];
    }

    /**
     * Indicate that the time log was auto-stopped.
     */
    public function autoStopped(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_auto_stopped' => true,
        ]);
    }

    /**
     * Indicate that the time log was manually stopped.
     */
    public function manuallyStopped(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_auto_stopped' => false,
        ]);
    }

    /**
     * Set a specific duration for the time log.
     */
    public function withDuration(int $hours): static
    {
        $startTime = fake()->dateTimeBetween('-1 month', 'now');
        $endTime = (clone $startTime)->modify('+' . $hours . ' hours');
        $duration = $endTime->getTimestamp() - $startTime->getTimestamp();

        return $this->state(fn (array $attributes) => [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => $duration,
        ]);
    }
}
