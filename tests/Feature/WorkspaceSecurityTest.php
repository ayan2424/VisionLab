<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_path_traversal_is_blocked_on_file_read()
    {
        $user = User::factory()->create(['role' => 'student']);
        $workspace = Workspace::factory()->create(['student_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/workspace/{$workspace->id}/read-file?path=../../../.env");

        $response->assertStatus(404);
    }

    public function test_workspace_isolation_prevents_access_to_other_users()
    {
        $user1 = User::factory()->create(['role' => 'student']);
        $user2 = User::factory()->create(['role' => 'student']);

        $workspace1 = Workspace::factory()->create(['student_id' => $user1->id]);

        $response = $this->actingAs($user2)->getJson("/workspace/{$workspace1->id}/files?path=index.js");

        $response->assertStatus(403);
    }
}
