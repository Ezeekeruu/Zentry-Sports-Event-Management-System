<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{                                        // ← opens class
    public function definition(): array
    {                                    // ← opens definition
        return [
            'first_name'        => fake()->firstName(),
            'last_name'         => fake()->lastName(),
            'email'             => fake()->unique()->safeEmail(),
            'password'          => Hash::make('password'),
            'role'              => fake()->randomElement(['organizer', 'coach', 'player', 'fan']),
            'is_active'         => true,
            'email_verified_at' => now(),
            'remember_token'    => Str::random(10),
        ];
    }                                    // ← closes definition

    public function admin(): static
    {
        return $this->state(['role' => 'admin']);
    }

    public function organizer(): static
    {
        return $this->state(['role' => 'organizer']);
    }

    public function coach(): static
    {
        return $this->state(['role' => 'coach']);
    }

    public function player(): static
    {
        return $this->state(['role' => 'player']);
    }

    public function fan(): static
    {
        return $this->state(['role' => 'fan']);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function unverified(): static
    {
        return $this->state(['email_verified_at' => null]);
    }
}                                       