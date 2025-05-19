<?php

namespace Database\Factories;

use App\Models\JobAssignment;
use App\Models\TaskProgress;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TaskProgress>
 */
class TaskProgressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TaskProgress::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $jobAssignment = JobAssignment::factory()->create();
        
        return [
            'job_assignment_id' => $jobAssignment->id,
            'freelancer_id' => $jobAssignment->freelancer_id,
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'file_path' => 'task_progress/' . $this->faker->uuid() . '.pdf',
            'original_filename' => $this->faker->word() . '.pdf',
            'mime_type' => 'application/pdf',
            'size' => $this->faker->numberBetween(1000, 5000000),
            'status' => $this->faker->randomElement(['submitted', 'approved', 'rejected']),
            'submitted_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
