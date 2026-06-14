<?php

namespace Tests\Feature;

use App\Events\PatchProposed;
use App\Models\AiPendingPatch;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * AiControllerTest — Verifies the Sovereign AI Agent proxy,
 * streamed completions, and human-in-the-loop (HITL) patch review controls.
 */
class AiControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createSetup(): array
    {
        $student = User::factory()->student()->create();
        $workspace = Workspace::create([
            'student_id' => $student->id,
            'course_id'  => null,
            'name'       => 'AI Sandbox Workspace',
            'status'     => 'pending',
            'language'   => 'python',
        ]);

        return compact('student', 'workspace');
    }

    // ── AI Streamed Completions ───────────────────────────────────────────

    public function test_authenticated_user_can_request_chat_completions(): void
    {
        Event::fake([PatchProposed::class]);
        $setup = $this->createSetup();

        // Let's assert completions endpoint yields streamed response in demo mode
        $response = $this->actingAs($setup['student'])
                         ->withHeader('X-Workspace-Id', 'ws-' . $setup['workspace']->id)
                         ->post(route('api.ai.chat.completions'), [
                             'messages' => [
                                 ['role' => 'user', 'content' => 'Please fix this file.'],
                             ],
                             'stream' => true,
                         ]);

        $response->assertOk();
        $this->assertStringContainsString('text/event-stream', $response->headers->get('Content-Type'));

        // Execute the streamed response callback to trigger generator iteration
        $response->sendContent();

        // Verify that demo-mode dispatches PatchProposed broadcast event
        Event::assertDispatched(PatchProposed::class);
    }

    // ── Human-In-The-Loop Patch Approvals & Rejections ────────────────────

    public function test_user_can_approve_pending_patch(): void
    {
        $setup = $this->createSetup();

        // Let's create workspace file directories to mock file modifications
        $wsDir = storage_path('workspaces/ws-' . $setup['workspace']->id);
        if (!is_dir($wsDir)) {
            mkdir($wsDir, 0755, true);
        }
        $setup['workspace']->update(['storage_path' => $wsDir]);

        // Create initial file content
        file_put_contents($wsDir . '/main.py', "print('old code')");

        $patch = AiPendingPatch::create([
            'workspace_id'     => $setup['workspace']->id,
            'file_path'        => 'main.py',
            'original_content' => "print('old code')",
            'patched_content'  => "print('Hello from AI!')",
            'diff'             => "+ print('Hello from AI!')\n- print('old code')",
            'status'           => 'pending',
            'created_by'       => $setup['student']->id,
        ]);

        $response = $this->actingAs($setup['student'])
                         ->post(route('api.ai.patches.approve', $patch->id));

        $response->assertOk()
                 ->assertJsonPath('message', 'Patch applied successfully.');

        // Verify status has updated to approved
        $this->assertEquals('approved', $patch->fresh()->status);

        // Verify code change was written back to storage path
        $this->assertEquals("print('Hello from AI!')", file_get_contents($wsDir . '/main.py'));

        // Clean up
        @unlink($wsDir . '/main.py');
        @rmdir($wsDir);
    }

    public function test_user_can_reject_pending_patch(): void
    {
        $setup = $this->createSetup();

        $patch = AiPendingPatch::create([
            'workspace_id'     => $setup['workspace']->id,
            'file_path'        => 'main.py',
            'original_content' => "print('old code')",
            'patched_content'  => "print('Hello from AI!')",
            'diff'             => "+ print('Hello from AI!')\n- print('old code')",
            'status'           => 'pending',
            'created_by'       => $setup['student']->id,
        ]);

        $response = $this->actingAs($setup['student'])
                         ->post(route('api.ai.patches.reject', $patch->id));

        $response->assertOk()
                 ->assertJsonPath('message', 'Patch rejected.');

        $this->assertEquals('rejected', $patch->fresh()->status);
    }

    public function test_cannot_approve_already_processed_patch(): void
    {
        $setup = $this->createSetup();

        $patch = AiPendingPatch::create([
            'workspace_id'     => $setup['workspace']->id,
            'file_path'        => 'main.py',
            'original_content' => "print('old')",
            'patched_content'  => "print('new')",
            'diff'             => "+ print('new')\n- print('old')",
            'status'           => 'approved',
            'created_by'       => $setup['student']->id,
        ]);

        $response = $this->actingAs($setup['student'])
                         ->post(route('api.ai.patches.approve', $patch->id));

        $response->assertStatus(400);
    }

    // ── Plan Execution ───────────────────────────────────────────────────

    public function test_user_can_trigger_plan_execution(): void
    {
        $setup = $this->createSetup();

        $response = $this->actingAs($setup['student'])
                         ->withHeader('X-Workspace-Id', 'ws-' . $setup['workspace']->id)
                         ->post(route('api.ai.patches.execute-plan'));

        $response->assertOk()
                 ->assertJsonPath('message', 'Plan execution started');
    }
}
