<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiAgentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Skip calling real Gemini API during tests
        config(['app.env' => 'testing']);
    }

    public function test_user_can_access_ai_chat()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/ai/chat', [
            'message' => 'explain this code',
            'mode' => 'CHAT',
            'context' => [
                'filename' => 'test.py',
                'language' => 'python',
                'code' => 'print("hello")'
            ]
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'code_snippet',
            'patch',
            'model',
            'mode'
        ]);
    }

    public function test_ai_agent_can_propose_patch()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/ai/propose-patch', [
            'instruction' => 'fix the bug',
            'code' => 'print(hello)',
            'language' => 'python',
            'filename' => 'test.py'
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'patch' => [
                'original',
                'patched',
                'file'
            ]
        ]);
    }
}
