<?php

namespace Database\Factories;

use App\Models\Announcement;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnnouncementFactory extends Factory
{
    protected $model = Announcement::class;

    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'author_id' => User::factory()->state(['role' => 'instructor']),
            'title'     => fake()->sentence(4),
            'body'      => fake()->paragraph(3),
            'pinned'    => false,
        ];
    }

    public function pinned(): static
    {
        return $this->state(fn () => ['pinned' => true]);
    }
}
