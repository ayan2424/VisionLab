<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiAgentAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_agent_cannot_write_files_directly()
    {
        $user = User::factory()->create(['role' => 'student']);
        $workspace = Workspace::factory()->create(['student_id' => $user->id]);

        $response = $this->actingAs($user)->postJson('/api/ai/execute', [
            'workspace_id' => $workspace->id,
            'tool' => 'write_file',
            'path' => 'index.js',
            'content' => 'console.log("hello");'
        ]);

        // It should either return 403 or create a pending patch, not write directly
        // Assuming your AiController returns 200 with the patch status for 'execute-plan'
        $response->assertStatus(403);
    }
}
