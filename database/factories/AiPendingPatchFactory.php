<?php

namespace Database\Factories;

use App\Models\AiPendingPatch;
use App\Models\AiChatSession;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Eloquent\Factories\Factory;

class AiPendingPatchFactory extends Factory
{
    protected $model = AiPendingPatch::class;

    public function definition(): array
    {
        $original = "def hello():\n    print('Hello')\n";
        $patched  = "def hello():\n    print('Hello, VisionLab!')\n";

        return [
            'workspace_id'     => Workspace::factory(),
            'session_id'       => AiChatSession::factory(),
            'file_path'        => 'main.py',
            'original_content' => $original,
            'patched_content'  => $patched,
            'diff'             => "-    print('Hello')\n+    print('Hello, VisionLab!')",
            'original_hash'    => hash('sha256', $original),
            'patched_hash'     => hash('sha256', $patched),
            'status'           => 'pending',
            'created_by'       => User::factory(),
            'reviewer_id'      => null,
            'reviewed_at'      => null,
            'expires_at'       => now()->addHours(24),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => [
            'status'      => 'approved',
            'reviewer_id' => User::factory(),
            'reviewed_at' => now(),
        ]);
    }

    public function rejected(): static
    {
        return $this->state(fn () => [
            'status'      => 'rejected',
            'reviewer_id' => User::factory(),
            'reviewed_at' => now(),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status'     => 'pending',
            'expires_at' => now()->subHour(),
        ]);
    }
}
