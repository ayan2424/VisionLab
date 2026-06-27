<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Deployment;
use App\Models\User;
use App\Models\Workspace;
use App\Services\CodeServerManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * DeploymentControllerTest — Verifies cloud deployments, authorization gates,
 * provider token missing failures, duplicate deployment locks, and GraphQL/Vercel API mocks.
 */
class DeploymentControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createWorkspaceForStudent(): array
    {
        $student = User::factory()->student()->create();
        $course = Course::factory()->create();
        $workspace = Workspace::factory()->create([
            'student_id' => $student->id,
            'course_id'  => $course->id,
            'name'       => 'test-student-workspace',
            'status'     => 'running',
        ]);

        return compact('student', 'course', 'workspace');
    }

    public function test_owner_can_deploy_to_vercel_successfully(): void
    {
        $data = $this->createWorkspaceForStudent();
        $workspace = $data['workspace'];
        $student = $data['student'];

        config(['visionlab.deploy.vercel_token' => 'mock-vercel-token']);

        // Mock CodeServerManager
        $csmMock = $this->createMock(CodeServerManager::class);
        $csmMock->method('listFiles')->willReturn([
            ['name' => 'index.html', 'type' => 'file', 'path' => 'index.html']
        ]);
        $csmMock->method('readFile')->willReturn('<h1>Hello World</h1>');
        $this->app->instance(CodeServerManager::class, $csmMock);

        // Fake Vercel API Response
        Http::fake([
            'api.vercel.com/*' => Http::response([
                'id'  => 'dpl_12345abcde',
                'url' => 'visionlab-test.vercel.app',
            ], 200)
        ]);

        $response = $this->actingAs($student)->postJson(route('workspace.deploy', $workspace->id), [
            'provider' => 'vercel',
            'name'     => 'my-custom-deploy',
        ]);

        $response->assertOk()
                 ->assertJsonPath('status', 'queued');

        $this->assertDatabaseHas('deployments', [
            'workspace_id'  => $workspace->id,
            'user_id'       => $student->id,
            'provider'      => 'vercel',
            'status'        => 'deployed',
            'deployment_id' => 'dpl_12345abcde',
            'public_url'    => 'https://visionlab-test.vercel.app',
        ]);
    }

    public function test_owner_can_deploy_to_railway_successfully(): void
    {
        $data = $this->createWorkspaceForStudent();
        $workspace = $data['workspace'];
        $student = $data['student'];

        config(['visionlab.deploy.railway_token' => 'mock-railway-token']);

        // Fake Railway GraphQL API Response
        Http::fake([
            'backboard.railway.app/*' => Http::response([
                'data' => [
                    'deploymentCreate' => [
                        'id'     => 'railway-dep-123',
                        'status' => 'SUCCESS',
                    ]
                ]
            ], 200)
        ]);

        $response = $this->actingAs($student)->postJson(route('workspace.deploy', $workspace->id), [
            'provider' => 'railway',
        ]);

        $response->assertOk()
                 ->assertJsonPath('status', 'queued');

        $this->assertDatabaseHas('deployments', [
            'workspace_id'  => $workspace->id,
            'user_id'       => $student->id,
            'provider'      => 'railway',
            'status'        => 'deployed',
            'deployment_id' => 'railway-dep-123',
            'public_url'    => 'https://railway-dep-123.up.railway.app',
        ]);
    }

    public function test_deployment_fails_when_token_is_missing(): void
    {
        $data = $this->createWorkspaceForStudent();
        $workspace = $data['workspace'];
        $student = $data['student'];

        // Ensure neither user token nor system token exists
        $student->update(['vercel_token' => null]);
        config(['visionlab.deploy.vercel_token' => null]);

        $response = $this->actingAs($student)->postJson(route('workspace.deploy', $workspace->id), [
            'provider' => 'vercel',
        ]);

        // Controller now validates token presence early and returns 422
        $response->assertStatus(422)
                 ->assertJsonStructure(['error']);
    }

    public function test_non_owner_cannot_deploy_workspace(): void
    {
        $data = $this->createWorkspaceForStudent();
        $workspace = $data['workspace'];
        $stranger = User::factory()->student()->create();

        $response = $this->actingAs($stranger)->postJson(route('workspace.deploy', $workspace->id), [
            'provider' => 'vercel',
        ]);

        $response->assertForbidden();
    }

    public function test_cannot_start_duplicate_active_deployments(): void
    {
        $data = $this->createWorkspaceForStudent();
        $workspace = $data['workspace'];
        $student = $data['student'];

        // Provide a valid token so we pass the token check and hit the duplicate check
        config(['visionlab.deploy.vercel_token' => 'mock-vercel-token']);

        // Setup an active build
        Deployment::create([
            'workspace_id' => $workspace->id,
            'user_id'      => $student->id,
            'provider'     => 'vercel',
            'status'       => 'building',
        ]);

        $response = $this->actingAs($student)->postJson(route('workspace.deploy', $workspace->id), [
            'provider' => 'vercel',
        ]);

        $response->assertStatus(409)
                 ->assertJsonPath('error', 'A deployment is already in progress.');
    }

    public function test_user_can_list_deployments(): void
    {
        $data = $this->createWorkspaceForStudent();
        $workspace = $data['workspace'];
        $student = $data['student'];
        $stranger = User::factory()->student()->create();

        Deployment::create([
            'workspace_id' => $workspace->id,
            'user_id'      => $student->id,
            'provider'     => 'vercel',
            'status'       => 'deployed',
        ]);

        // Stranger cannot view
        $this->actingAs($stranger)->getJson(route('workspace.deployments', $workspace->id))
             ->assertForbidden();

        // Owner can view
        $response = $this->actingAs($student)->getJson(route('workspace.deployments', $workspace->id));
        $response->assertOk()
                 ->assertJsonCount(1);
    }

    public function test_user_can_cancel_deployment(): void
    {
        $data = $this->createWorkspaceForStudent();
        $workspace = $data['workspace'];
        $student = $data['student'];
        $stranger = User::factory()->student()->create();

        $deployment = Deployment::create([
            'workspace_id' => $workspace->id,
            'user_id'      => $student->id,
            'provider'     => 'vercel',
            'status'       => 'queued',
        ]);

        // Stranger cannot cancel
        $this->actingAs($stranger)->deleteJson(route('deployments.destroy', $deployment->id))
             ->assertStatus(403);

        // Owner can cancel
        $response = $this->actingAs($student)->deleteJson(route('deployments.destroy', $deployment->id));
        $response->assertOk()
                 ->assertJsonPath('status', 'cancelled');

        $this->assertDatabaseHas('deployments', [
            'id'            => $deployment->id,
            'status'        => 'failed',
            'error_summary' => 'Cancelled by user',
        ]);
    }
}
