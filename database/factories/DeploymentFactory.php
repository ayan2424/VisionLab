<?php

namespace Database\Factories;

use App\Models\Deployment;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeploymentFactory extends Factory
{
    protected $model = Deployment::class;

    public function definition(): array
    {
        return [
            'workspace_id'     => Workspace::factory(),
            'user_id'          => User::factory()->state(['role' => 'student']),
            'provider'         => 'vercel',
            'deployment_id'    => null,
            'public_url'       => null,
            'status'           => 'queued',
            'job_metadata'     => null,
            'error_summary'    => null,
            'deployed_at'      => null,
            'notification_sent' => false,
        ];
    }

    public function deployed(): static
    {
        return $this->state(fn () => [
            'status'            => 'deployed',
            'deployment_id'     => 'dpl_' . fake()->regexify('[a-zA-Z0-9]{20}'),
            'public_url'        => 'https://' . fake()->domainWord() . '.vercel.app',
            'deployed_at'       => now(),
            'notification_sent' => true,
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn () => [
            'status'        => 'failed',
            'error_summary' => 'Build failed: missing package.json',
        ]);
    }
}
