<?php

namespace Database\Factories;

use App\Models\BudgetAppeal;
use App\Models\JobAssignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BudgetAppealFactory extends Factory
{
    protected $model = BudgetAppeal::class;

    public function definition(): array
    {
        $jobAssignment = JobAssignment::factory()->create();

        return [
            'job_assignment_id' => $jobAssignment->id,
            'freelancer_id' => $jobAssignment->freelancer_id, // Use the freelancer from the job assignment
            'current_budget' => $this->faker->numberBetween(500, 1000),
            'requested_budget' => $this->faker->numberBetween(1000, 2000),
            'reason' => $this->faker->paragraph,
            'evidence_path' => null, // Or $this->faker->optional()->filePath(),
            'status' => $this->faker->randomElement([BudgetAppeal::STATUS_PENDING, BudgetAppeal::STATUS_APPROVED, BudgetAppeal::STATUS_REJECTED]),
            'admin_remarks' => $this->faker->optional()->paragraph,
            'client_decision' => $this->faker->optional()->randomElement(['approved', 'rejected']),
            'client_remarks' => $this->faker->optional()->paragraph,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BudgetAppeal::STATUS_PENDING,
        ]);
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BudgetAppeal::STATUS_APPROVED,
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => BudgetAppeal::STATUS_REJECTED,
        ]);
    }
}
