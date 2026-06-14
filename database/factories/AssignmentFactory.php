<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    protected $model = Assignment::class;

    public function definition(): array
    {
        return [
            'course_id'        => Course::factory(),
            'title'            => fake()->sentence(4),
            'description'      => fake()->paragraph(2),
            'max_points'       => fake()->randomElement([50, 100, 150, 200]),
            'due_date'         => now()->addDays(fake()->numberBetween(3, 30)),
            'starter_code'     => null,
            'starter_language' => 'python',
            'auto_workspace'   => true,
        ];
    }

    public function overdue(): static
    {
        return $this->state(fn () => ['due_date' => now()->subDays(3)]);
    }

    public function noDueDate(): static
    {
        return $this->state(fn () => ['due_date' => null]);
    }

    public function withStarterCode(): static
    {
        return $this->state(fn () => [
            'starter_code' => "# Starter Code\nprint('Hello, VisionLab!')\n",
        ]);
    }
}
