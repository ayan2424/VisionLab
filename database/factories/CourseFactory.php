<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CourseFactory extends Factory
{
    protected $model = Course::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);

        return [
            'instructor_id'   => User::factory()->state(['role' => 'instructor']),
            'title'           => $title,
            'slug'            => Str::slug($title) . '-' . Str::random(4),
            'description'     => fake()->paragraph(3),
            'cover_image'     => null,
            'enrollment_code' => strtoupper(Str::random(6)),
            'is_active'       => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
