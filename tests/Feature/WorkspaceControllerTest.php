<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceTemplate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * WorkspaceControllerTest — Verifies container workspaces, Web/API operations,
 * authorization gates, and path traversal protections.
 */
class WorkspaceControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createWorkspaceForStudent(): array
    {
        $student    = User::factory()->student()->create();
        $instructor = User::factory()->instructor()->create();
        $course     = Course::factory()->create(['instructor_id' => $instructor->id]);

        $workspace = Workspace::factory()->create([
            'student_id' => $student->id,
            'course_id'  => $course->id,
            'name'       => 'test-student-workspace',
            'status'     => 'pending',
        ]);

        return compact('student', 'instructor', 'course', 'workspace');
    }

    // ── Provisioning & Access ─────────────────────────────────────────────

    public function test_student_can_provision_personal_workspace(): void
    {
        $student = User::factory()->student()->create();
        $template = WorkspaceTemplate::create([
            'name'        => 'Python 3.12 Lab',
            'slug'        => 'python-312-lab',
            'description' => 'Standard Python environment',
            'language'    => 'python',
            'image'       => 'visionlab/workspace:latest',
            'is_active'   => true,
        ]);

        $indexResponse = $this->actingAs($student)->get(route('workspace.index'));
        $indexResponse->assertOk();

        $storeResponse = $this->actingAs($student)->post(route('workspace.store'), [
            'name'        => 'My Workspace',
            'template_id' => $template->id,
        ]);

        $workspace = Workspace::where('student_id', $student->id)->firstOrFail();
        $storeResponse->assertRedirect(route('workspace.show', $workspace->slug));

        $this->assertDatabaseHas('workspaces', [
            'student_id' => $student->id,
            'course_id'  => null,
            'name'       => 'My Workspace',
            'status'     => 'pending',
        ]);
    }

    public function test_student_can_access_own_workspace(): void
    {
        $data = $this->createWorkspaceForStudent();

        $response = $this->actingAs($data['student'])
                         ->get(route('workspace.show', $data['workspace']));

        $response->assertOk();
    }

    public function test_student_cannot_access_other_students_workspace(): void
    {
        $data = $this->createWorkspaceForStudent();
        $otherStudent = User::factory()->student()->create();

        $response = $this->actingAs($otherStudent)
                         ->get(route('workspace.show', $data['workspace']));

        $response->assertForbidden();
    }

    public function test_instructor_can_access_student_workspace_in_own_course(): void
    {
        $data = $this->createWorkspaceForStudent();

        $response = $this->actingAs($data['instructor'])
                         ->get(route('workspace.show', $data['workspace']));

        $response->assertOk();
    }

    public function test_instructor_cannot_access_workspace_in_other_instructor_course(): void
    {
        $data = $this->createWorkspaceForStudent();
        $otherInstructor = User::factory()->instructor()->create();

        $response = $this->actingAs($otherInstructor)
                         ->get(route('workspace.show', $data['workspace']));

        $response->assertForbidden();
    }

    // ── Lifecycle Management ──────────────────────────────────────────────

    public function test_owner_can_start_stop_restart_workspace(): void
    {
        $data = $this->createWorkspaceForStudent();

        // Start
        $response = $this->actingAs($data['student'])
                         ->post(route('workspace.start', $data['workspace']));
        $response->assertOk()
                 ->assertJsonPath('status', 'running');

        // Status
        $response = $this->actingAs($data['student'])
                         ->get(route('workspace.status', $data['workspace']));
        $response->assertOk()
                 ->assertJsonStructure(['status', 'running', 'url']);

        // Restart preloader view & process endpoint
        $this->actingAs($data['student'])
             ->get(route('workspace.restart', $data['workspace']))
             ->assertOk();

        $response = $this->actingAs($data['student'])
                         ->post(route('workspace.process_restart', $data['workspace']));
        $response->assertOk()
                 ->assertJsonPath('status', 'restarting');

        // Stop
        $response = $this->actingAs($data['student'])
                         ->post(route('workspace.stop', $data['workspace']));
        $response->assertOk()
                 ->assertJsonPath('status', 'stopped');
    }

    // ── Sandboxed File Operations ─────────────────────────────────────────

    public function test_owner_can_perform_file_operations(): void
    {
        $data = $this->createWorkspaceForStudent();

        $wsDir = storage_path('workspaces/ws-' . $data['workspace']->id);
        if (!is_dir($wsDir)) {
            mkdir($wsDir, 0755, true);
        }
        $data['workspace']->update(['storage_path' => $wsDir]);

        // Create File
        $response = $this->actingAs($data['student'])
                         ->post(route('workspace.createFile', $data['workspace']), [
                             'path'         => 'index.py',
                             'is_directory' => false,
                         ]);
        $response->assertOk()
                 ->assertJsonPath('status', 'created');
        $this->assertFileExists($wsDir . '/index.py');

        // Write File
        $response = $this->actingAs($data['student'])
                         ->post(route('workspace.writeFile', $data['workspace']), [
                             'path'    => 'index.py',
                             'content' => 'print("Hello VisionLab")',
                         ]);
        $response->assertOk()
                 ->assertJsonPath('status', 'written');
        $this->assertEquals('print("Hello VisionLab")', file_get_contents($wsDir . '/index.py'));

        // Read File
        $response = $this->actingAs($data['student'])
                         ->get(route('workspace.readFile', $data['workspace']) . '?path=index.py');
        $response->assertOk()
                 ->assertJsonPath('content', 'print("Hello VisionLab")');

        // Rename File
        $response = $this->actingAs($data['student'])
                         ->post(route('workspace.renameFile', $data['workspace']), [
                             'old_path' => 'index.py',
                             'new_path' => 'main.py',
                         ]);
        $response->assertOk()
                 ->assertJsonPath('status', 'renamed');
        $this->assertFileExists($wsDir . '/main.py');

        // List Files
        $response = $this->actingAs($data['student'])
                         ->get(route('workspace.files', $data['workspace']));
        $response->assertOk()
                 ->assertJsonIsArray();

        // Delete File
        $response = $this->actingAs($data['student'])
                         ->delete(route('workspace.deleteFile', $data['workspace']), [
                             'path' => 'main.py',
                         ]);
        $response->assertOk()
                 ->assertJsonPath('status', 'deleted');
        $this->assertFileDoesNotExist($wsDir . '/main.py');

        File::deleteDirectory($wsDir);
    }

    // ── Security & Path Traversal Protections ──────────────────────────────

    public function test_workspace_file_operations_deny_path_traversal(): void
    {
        $data = $this->createWorkspaceForStudent();

        $wsDir = storage_path('workspaces/ws-' . $data['workspace']->id);
        if (!is_dir($wsDir)) {
            mkdir($wsDir, 0755, true);
        }
        $data['workspace']->update(['storage_path' => $wsDir]);

        // Attempting to read outside workspace root using parent traversal
        $response = $this->actingAs($data['student'])
                         ->get(route('workspace.readFile', $data['workspace']) . '?path=../../.env');
        $response->assertStatus(404);

        // Attempting to write outside workspace root
        $response = $this->actingAs($data['student'])
                         ->post(route('workspace.writeFile', $data['workspace']), [
                             'path'    => '../../hack.php',
                             'content' => '<?php system($_GET["cmd"]);',
                         ]);
        $response->assertStatus(403);

        File::deleteDirectory($wsDir);
    }
}
