<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FreelancerProfile>
 */
class FreelancerProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(['role' => User::ROLE_FREELANCER])->id,
            'skills' => implode(',', fake()->words(5)),
            'portfolio_link' => fake()->url(),
            'hourly_rate' => fake()->numberBetween(20, 100),
            'bio' => fake()->paragraphs(2, true),
            'profile_picture_path' => fake()->optional(0.7)->imageUrl(),
            'availability' => fake()->randomElement(['full_time', 'part_time', 'weekends_only']),
            'experience_level' => fake()->randomElement(['beginner', 'intermediate', 'expert']),
            'receive_new_job_notifications' => fake()->boolean(80),
        ];
    }

    /**
     * Indicate that the freelancer is a beginner.
     */
    public function beginner(): static
    {
        return $this->state(fn (array $attributes) => [
            'experience_level' => 'beginner',
            'hourly_rate' => fake()->numberBetween(20, 40),
        ]);
    }

    /**
     * Indicate that the freelancer is intermediate.
     */
    public function intermediate(): static
    {
        return $this->state(fn (array $attributes) => [
            'experience_level' => 'intermediate',
            'hourly_rate' => fake()->numberBetween(40, 70),
        ]);
    }

    /**
     * Indicate that the freelancer is an expert.
     */
    public function expert(): static
    {
        return $this->state(fn (array $attributes) => [
            'experience_level' => 'expert',
            'hourly_rate' => fake()->numberBetween(70, 150),
        ]);
    }
}
