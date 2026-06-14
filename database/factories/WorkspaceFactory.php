<?php

namespace Database\Factories;

use App\Models\Workspace;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WorkspaceFactory extends Factory
{
    protected $model = Workspace::class;

    public function definition(): array
    {
        return [
            'course_id'     => Course::factory(),
            'assignment_id' => null,
            'student_id'    => User::factory()->state(['role' => 'student']),
            'name'          => 'workspace-' . Str::random(8),
            'container_id'  => null,
            'port'          => fake()->unique()->numberBetween(10000, 60000),
            'token'         => Str::random(64),
            'storage_path'  => null,
            'heartbeat_at'  => null,
            'quota_data'    => null,
            'proxy_url'     => null,
            'container_image' => null,
            'status'        => 'pending',
            'language'      => 'python',
        ];
    }

    public function running(): static
    {
        return $this->state(fn () => [
            'status'       => 'running',
            'container_id' => 'sha256:' . Str::random(12),
            'heartbeat_at' => now(),
        ]);
    }

    public function stopped(): static
    {
        return $this->state(fn () => ['status' => 'stopped']);
    }

    public function error(): static
    {
        return $this->state(fn () => ['status' => 'error']);
    }
}
