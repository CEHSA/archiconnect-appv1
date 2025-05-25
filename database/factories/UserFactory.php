<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'role' => 'user',
            'is_admin' => false,
        ];
    }

    public function admin(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'admin',
                'is_admin' => true,
            ];
        });
    }

    public function client(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'client',
            ];
        });
    }

    public function freelancer(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'freelancer',
            ];
        });
    }

    public function unverified(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
