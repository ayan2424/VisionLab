<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'             => fake()->name(),
            'email'            => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'         => static::$password ??= Hash::make('password'),
            'role'             => 'student',
            'status'           => 'active',
            'theme_preference' => 'dark',
            'remember_token'   => Str::random(10),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => 'admin']);
    }

    public function instructor(): static
    {
        return $this->state(fn () => ['role' => 'instructor']);
    }

    public function student(): static
    {
        return $this->state(fn () => ['role' => 'student']);
    }

    public function suspended(): static
    {
        return $this->state(fn () => ['status' => 'suspended']);
    }
}
