<?php

namespace Database\Factories;

use App\Models\AiChatSession;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiChatSessionFactory extends Factory
{
    protected $model = AiChatSession::class;

    public function definition(): array
    {
        return [
            'user_id'           => User::factory(),
            'workspace_id'      => null,
            'workspace_ref'     => null,
            'title'             => 'New Chat',
            'mode'              => 'CHAT',
            'token_total'       => 0,
            'context_metadata'  => null,
            'provider_metadata' => null,
        ];
    }

    public function planMode(): static
    {
        return $this->state(fn () => ['mode' => 'PLAN', 'title' => 'Implementation Plan']);
    }

    public function agentMode(): static
    {
        return $this->state(fn () => ['mode' => 'AGENT', 'title' => 'Agent Session']);
    }

    public function withWorkspace(): static
    {
        return $this->state(fn () => [
            'workspace_id' => Workspace::factory(),
        ]);
    }
}
