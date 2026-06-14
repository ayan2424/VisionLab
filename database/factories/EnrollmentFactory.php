<?php

namespace Database\Factories;

use App\Models\Enrollment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrollmentFactory extends Factory
{
    protected $model = Enrollment::class;

    public function definition(): array
    {
        return [
            'course_id'   => Course::factory(),
            'student_id'  => User::factory()->state(['role' => 'student']),
            'status'      => 'active',
            'enrolled_at' => now(),
        ];
    }

    public function dropped(): static
    {
        return $this->state(fn () => ['status' => 'dropped']);
    }

    public function invited(): static
    {
        return $this->state(fn () => ['status' => 'invited']);
    }
}
