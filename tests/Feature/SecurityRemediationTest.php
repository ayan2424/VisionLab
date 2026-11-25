<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Jobs\WorkspaceSuspendedUser;
use App\Models\AiPendingPatch;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Extension;
use App\Models\Submission;
use App\Models\User;
use App\Models\Workspace;
use App\Services\CodeServerManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SecurityRemediationTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_controller_blocks_cross_user_idor_across_all_endpoints(): void
    {
        $victim = User::factory()->student()->create();
        $intruder = User::factory()->student()->create();

        $workspace = Workspace::factory()->create([
            'student_id' => $victim->id,
            'status'     => 'running',
        ]);

        $patch = AiPendingPatch::create([
            'workspace_id'     => $workspace->id,
            'file_path'        => 'src/app.py',
            'original_content' => 'x = 1',
            'patched_content'  => 'x = 2',
            'diff'             => "-x = 1\n+x = 2",
            'status'           => 'pending',
            'created_by'       => $victim->id,
        ]);

        // 1. chatCompletions IDOR blocked
        $this->actingAs($intruder)
            ->withHeader('X-Workspace-Id', 'ws-' . $workspace->id)
            ->postJson(route('api.ai.chat.completions'), [
                'messages' => [['role' => 'user', 'content' => 'Read victim files']],
                'stream'   => true,
            ])
            ->assertForbidden();

        // 2. pendingPatches IDOR blocked
        $this->actingAs($intruder)
            ->getJson(route('api.ai.patches.pending', ['workspace_id' => 'ws-' . $workspace->id]))
            ->assertForbidden();

        // 3. approvePatch IDOR blocked
        $this->actingAs($intruder)
            ->postJson(route('api.ai.patches.approve', $patch->id))
            ->assertForbidden();

        // 4. rejectPatch IDOR blocked
        $this->actingAs($intruder)
            ->postJson(route('api.ai.patches.reject', $patch->id))
            ->assertForbidden();

        // 5. executePlan IDOR blocked
        $this->actingAs($intruder)
            ->postJson(route('api.ai.patches.execute-plan'), [
                'workspaceId' => 'ws-' . $workspace->id,
            ])
            ->assertForbidden();

        // Patch status remained untouched
        $this->assertSame('pending', $patch->fresh()->status);
    }

    public function test_code_server_manager_blocks_traversal_sensitive_files_and_shell_injection(): void
    {
        $owner = User::factory()->student()->create();
        $workspace = Workspace::factory()->create(['student_id' => $owner->id]);
        $manager = app(CodeServerManager::class);

        $maliciousPaths = [
            '../.env',
            '../../etc/passwd',
            '/etc/passwd',
            '~/.ssh/id_rsa',
            '.env',
            '.env.remote',
            '.git/config',
            'vendor/autoload.php',
            'node_modules/chalk/index.js',
            "foo';id;#",
            '$(whoami).py',
            '`id`.py',
            'src/main.py|cat /etc/passwd',
            "main.py\0.txt",
            'C:/Windows/System32/cmd.exe',
        ];

        foreach ($maliciousPaths as $badPath) {
            $this->assertNull(
                $manager->sanitizeWorkspaceRelativePath($badPath),
                "Expected path [{$badPath}] to be rejected."
            );
            $this->assertFalse($manager->writeFile($workspace, $badPath, 'evil'));
            $this->assertNull($manager->readFile($workspace, $badPath));
            $this->assertFalse($manager->deleteFile($workspace, $badPath));
            $this->assertFalse($manager->createFile($workspace, $badPath));
        }

        $allowedPaths = [
            'src/main.py'            => 'src/main.py',
            './src/utils/helper.ts'  => 'src/utils/helper.ts',
            'app/[id]/page.tsx'      => 'app/[id]/page.tsx',
            '.vision/dev.nix'        => '.vision/dev.nix',
            '.visioncode_memory.md'  => '.visioncode_memory.md',
        ];

        foreach ($allowedPaths as $input => $expected) {
            $this->assertSame($expected, $manager->sanitizeWorkspaceRelativePath($input));
        }
    }

    public function test_private_broadcast_channels_enforce_workspace_authorization(): void
    {
        config([
            'broadcasting.default' => 'pusher',
            'broadcasting.connections.pusher.key' => '1234567890abcdef1234',
            'broadcasting.connections.pusher.secret' => '1234567890abcdef1234',
            'broadcasting.connections.pusher.app_id' => '12345',
        ]);

        $instructor = User::factory()->instructor()->create();
        $owner = User::factory()->student()->create();
        $intruder = User::factory()->student()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $workspace = Workspace::factory()->create([
            'student_id' => $owner->id,
            'course_id'  => $course->id,
        ]);

        foreach (['private-collab.ws-' . $workspace->id, 'private-workspace.' . $workspace->slug . '.patches'] as $channel) {
            $this->actingAs($owner)
                ->postJson('/broadcasting/auth', ['channel_name' => $channel, 'socket_id' => '123.456'])
                ->assertOk();

            $this->actingAs($instructor)
                ->postJson('/broadcasting/auth', ['channel_name' => $channel, 'socket_id' => '123.456'])
                ->assertOk();

            $this->actingAs($intruder)
                ->postJson('/broadcasting/auth', ['channel_name' => $channel, 'socket_id' => '123.456'])
                ->assertForbidden();
        }
    }

    public function test_user_add_xp_persists_transaction_without_fatal_error(): void
    {
        $student = User::factory()->student()->create(['xp' => 0, 'level' => 1]);
        $workspace = Workspace::factory()->create(['student_id' => $student->id]);

        $student->addXp(150, 'Completed lab benchmark', $workspace);

        $student->refresh();
        $this->assertSame(150, $student->xp);
        $this->assertSame(2, $student->level);
        $this->assertDatabaseHas('xp_transactions', [
            'user_id'     => $student->id,
            'amount'      => 150,
            'source_type' => Workspace::class,
            'source_id'   => $workspace->id,
        ]);
    }

    public function test_course_extension_update_and_admin_workspace_show_execute_cleanly(): void
    {
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->student()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $workspace = Workspace::factory()->create([
            'student_id' => $student->id,
            'course_id'  => $course->id,
        ]);
        $extension = Extension::factory()->create(['is_active' => true, 'is_global' => false]);

        $this->actingAs($instructor)
            ->put(route('courses.extensions.update', $course), [
                'extensions' => [
                    ['id' => $extension->id, 'is_required' => true],
                ],
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('course_extensions', [
            'course_id'    => $course->id,
            'extension_id' => $extension->id,
            'is_required'  => true,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.workspaces.show', $workspace))
            ->assertOk()
            ->assertSee($workspace->slug);
    }

    public function test_forensics_sync_persists_using_real_submission_forensics_columns(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student = User::factory()->student()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);
        $assignment = Assignment::factory()->create(['course_id' => $course->id]);
        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'student_id'    => $student->id,
            'status'        => 'in_progress',
        ]);
        $workspace = Workspace::factory()->create([
            'student_id' => $student->id,
            'course_id'  => $course->id,
        ]);

        $response = $this->actingAs($student)
            ->postJson("/api/workspace/ws-{$workspace->id}/forensics/sync", [
                'humanKeystrokeDelta' => 80,
                'aiInjectedCharDelta' => 20,
            ]);

        $response->assertOk()
            ->assertJsonPath('status', 'synced')
            ->assertJsonPath('forensics.human_pct', 80)
            ->assertJsonPath('forensics.ai_pct', 20);

        $this->assertDatabaseHas('submission_forensics', [
            'submission_id'      => $submission->id,
            'workspace_id'       => $workspace->id,
            'human_keystrokes'   => 80,
            'ai_patches_applied' => 20,
        ]);
    }

    public function test_admin_suspend_revokes_sanctum_tokens_and_dispatches_workspace_suspension_job(): void
    {
        Queue::fake();

        $admin = User::factory()->admin()->create();
        $student = User::factory()->student()->create(['status' => 'active']);
        $student->createToken('ide-agent');
        $this->assertSame(1, $student->tokens()->count());

        $this->actingAs($admin)
            ->post(route('admin.users.suspend', $student))
            ->assertRedirect();

        $this->assertSame('suspended', $student->fresh()->status);
        $this->assertSame(0, $student->tokens()->count());
        Queue::assertPushed(WorkspaceSuspendedUser::class, fn ($job) => $job->userId === $student->id);
    }

    public function test_gitignore_excludes_all_dotenv_variants_except_example(): void
    {
        $gitignore = file_get_contents(base_path('.gitignore'));
        $this->assertIsString($gitignore);
        $this->assertStringContainsString('.env.*', $gitignore);
        $this->assertStringContainsString('!.env.example', $gitignore);
    }
}
