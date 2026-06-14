<?php

namespace Database\Factories;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class AuditLogFactory extends Factory
{
    protected $model = AuditLog::class;

    public function definition(): array
    {
        return [
            'actor_id'       => User::factory(),
            'action'         => fake()->randomElement(['user.login', 'course.create', 'workspace.start', 'ai.patch.approve', 'admin.user.suspend']),
            'resource_type'  => fake()->randomElement(['User', 'Course', 'Workspace', 'AiPendingPatch', 'Extension']),
            'resource_id'    => fake()->numberBetween(1, 100),
            'old_state'      => null,
            'new_state'      => null,
            'result'         => 'success',
            'ip_address'     => fake()->ipv4(),
            'user_agent'     => fake()->userAgent(),
            'correlation_id' => Str::uuid()->toString(),
        ];
    }
}
