<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_code_server_manager_blocks_path_traversal(): void
    {
        $user = \App\Models\User::factory()->create();
        $workspace = \App\Models\Workspace::create([
            'student_id' => $user->id,
            'name' => 'test-workspace',
            'status' => 'pending',
        ]);

        $manager = new \App\Services\CodeServerManager();
        
        // Ensure directory exists for realpath() to work
        $basePath = storage_path('workspaces' . DIRECTORY_SEPARATOR . 'ws-' . $workspace->id);
        if (!is_dir($basePath)) {
            mkdir($basePath, 0755, true);
        }
        
        // Allowed path
        $allowedPath = $manager->resolveSecurePath($workspace, 'main.py');
        $this->assertNotNull($allowedPath);
        $this->assertStringContainsString('main.py', $allowedPath);

        // Path Traversal Attempt
        $blockedPath = $manager->resolveSecurePath($workspace, '../../../../etc/passwd');
        $this->assertNull($blockedPath);
    }

    public function test_quota_resolution_follows_priority(): void
    {
        $globalQuota = \App\Models\WorkspaceQuota::create([
            'name' => 'Global Default',
            'scope' => 'global',
            'memory_mb' => 512,
            'is_active' => true,
        ]);

        $user = \App\Models\User::factory()->create();
        $userQuota = \App\Models\WorkspaceQuota::create([
            'name' => 'User Specific',
            'scope' => 'user',
            'scope_id' => $user->id,
            'memory_mb' => 2048,
            'is_active' => true,
        ]);

        $resolved = \App\Models\WorkspaceQuota::resolveFor($user->id, null);
        $this->assertEquals(2048, $resolved->memory_mb);
        $this->assertEquals('user', $resolved->scope);
    }
}
