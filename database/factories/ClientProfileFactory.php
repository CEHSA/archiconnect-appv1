<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ClientProfile>
 */
class ClientProfileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->create(['role' => User::ROLE_CLIENT])->id,
            'company_name' => fake()->company(),
            'project_preferences' => json_encode(fake()->randomElements(['residential', 'commercial', 'industrial', 'landscape', 'interior'], rand(1, 3))),
            'contact_details' => fake()->phoneNumber(),
            'company_website' => fake()->optional(0.8)->url(),
            'industry' => fake()->randomElement(['architecture', 'construction', 'real_estate', 'interior_design', 'urban_planning']),
        ];
    }

    /**
     * Indicate that the client is in the architecture industry.
     */
    public function architecture(): static
    {
        return $this->state(fn (array $attributes) => [
            'industry' => 'architecture',
            'project_preferences' => ['residential', 'commercial'],
        ]);
    }

    /**
     * Indicate that the client is in the construction industry.
     */
    public function construction(): static
    {
        return $this->state(fn (array $attributes) => [
            'industry' => 'construction',
            'project_preferences' => ['commercial', 'industrial'],
        ]);
    }

    /**
     * Indicate that the client is in the real estate industry.
     */
    public function realEstate(): static
    {
        return $this->state(fn (array $attributes) => [
            'industry' => 'real_estate',
            'project_preferences' => ['residential', 'commercial'],
        ]);
    }
}
