<?php

namespace Database\Factories;

use App\Models\Submission;
use App\Models\Assignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionFactory extends Factory
{
    protected $model = Submission::class;

    public function definition(): array
    {
        return [
            'assignment_id'          => Assignment::factory(),
            'student_id'             => User::factory()->state(['role' => 'student']),
            'workspace_snapshot_path' => null,
            'code_snapshot'          => "print('Hello World')\n",
            'status'                 => 'in_progress',
            'submitted_at'           => null,
            'grade'                  => null,
            'feedback'               => null,
            'graded_by'              => null,
        ];
    }

    public function submitted(): static
    {
        return $this->state(fn () => [
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);
    }

    public function graded(int $grade = 85): static
    {
        return $this->state(fn () => [
            'status'       => 'graded',
            'submitted_at' => now()->subDay(),
            'grade'        => $grade,
            'feedback'     => fake()->sentence(),
            'graded_by'    => User::factory()->state(['role' => 'instructor']),
        ]);
    }
}
