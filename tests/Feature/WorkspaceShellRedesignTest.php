<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AiPendingPatch;
use App\Models\User;
use App\Models\Workspace;
use App\Services\CodeServerManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class WorkspaceShellRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_workspace_index_and_create_render_on_luminous_atelier_without_orange_or_neon_slop(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $indexResponse = $this->actingAs($student)->get('/workspace');
        $indexResponse->assertOk();
        $indexResponse->assertDontSee('#f97316', false);
        $indexResponse->assertDontSee('#B026FF', false);

        $createResponse = $this->actingAs($student)->get('/workspace/create');
        $createResponse->assertOk();
        $createResponse->assertDontSee('#f97316', false);
        $createResponse->assertDontSee('#B026FF', false);
    }

    public function test_workspace_status_returns_pending_patches_count_and_denies_non_owners(): void
    {
        $owner = User::factory()->create(['role' => 'student']);
        $intruder = User::factory()->create(['role' => 'student']);

        $workspace = Workspace::create([
            'student_id' => $owner->id,
            'name' => 'CS-301 Lab',
            'slug' => 'cs301-lab-' . $owner->id,
            'language' => 'python',
            'status' => 'running',
        ]);

        AiPendingPatch::create([
            'workspace_id' => $workspace->id,
            'created_by' => $owner->id,
            'file_path' => 'src/main.py',
            'original_content' => 'x = 1',
            'patched_content' => 'x = 2',
            'diff' => "-x = 1\n+x = 2",
            'status' => 'pending',
        ]);

        $mock = Mockery::mock(CodeServerManager::class);
        $mock->shouldReceive('getStatus')->andReturn([
            'running' => true,
            'url' => 'http://127.0.0.1:8080',
        ]);
        $mock->shouldReceive('getStats')->andReturn([
            'cpu' => '12%',
            'memory' => '340MB',
        ]);
        $this->app->instance(CodeServerManager::class, $mock);

        $this->actingAs($owner)
            ->getJson("/workspace/{$workspace->slug}/status")
            ->assertOk()
            ->assertJsonPath('running', true)
            ->assertJsonPath('pending_patches_count', 1);

        $this->actingAs($intruder)
            ->getJson("/workspace/{$workspace->slug}/status")
            ->assertForbidden();
    }
}
