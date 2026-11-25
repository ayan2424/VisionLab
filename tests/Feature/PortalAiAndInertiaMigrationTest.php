<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PortalAiAndInertiaMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_call_portal_ai_chat(): void
    {
        $this->postJson('/api/portal-ai/chat', ['message' => 'hello'])
            ->assertUnauthorized();
    }

    public function test_student_can_chat_with_portal_ai_copilot(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student)
            ->postJson('/api/portal-ai/chat', [
                'message'    => 'What deadlines and workspaces do I have?',
                'page_title' => 'Student Workspace',
                'page_url'   => '/dashboard',
            ])
            ->assertOk()
            ->assertJsonStructure(['reply', 'mode', 'actions', 'context_badge'])
            ->assertJsonPath('context_badge', fn (string $v) => str_contains($v, 'Student Copilot'));
    }

    public function test_instructor_can_chat_with_portal_ai_copilot(): void
    {
        $instructor = User::factory()->instructor()->create();

        $this->actingAs($instructor)
            ->postJson('/api/portal-ai/chat', [
                'message'    => 'Triage my grading queue',
                'page_title' => 'Faculty Studio',
                'page_url'   => '/dashboard',
            ])
            ->assertOk()
            ->assertJsonStructure(['reply', 'mode', 'actions', 'context_badge'])
            ->assertJsonPath('context_badge', fn (string $v) => str_contains($v, 'Instructor Copilot'));
    }

    public function test_admin_can_chat_with_portal_ai_copilot(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->postJson('/api/portal-ai/chat', [
                'message'    => 'Summarize cluster containers and ERP',
                'page_title' => 'Command Center',
                'page_url'   => '/admin',
            ])
            ->assertOk()
            ->assertJsonStructure(['reply', 'mode', 'actions', 'context_badge'])
            ->assertJsonPath('context_badge', fn (string $v) => str_contains($v, 'Admin Copilot'));
    }

    public function test_migrated_student_and_lms_routes_render_inertia_react_pages(): void
    {
        $student = User::factory()->student()->create();

        $this->actingAs($student)->get('/courses')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Courses/Index'));

        $this->actingAs($student)->get('/join')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Enrollments/Join'));

        $this->actingAs($student)->get('/announcements')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Announcements/Index'));

        $this->actingAs($student)->get('/progress')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Progress/Index'));

        $this->actingAs($student)->get('/gamification')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Gamification/Index'));

        $this->actingAs($student)->get('/profile')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Profile/Edit'));

        $this->get('/offline')
            ->assertOk()
            ->assertInertia(fn (Assert $p) => $p->component('Public/Offline'));
    }

    public function test_migrated_admin_governance_and_erp_routes_render_inertia_react_pages(): void
    {
        $admin = User::factory()->admin()->create();
        $instructor = User::factory()->instructor()->create();
        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        $routes = [
            '/admin/analytics'           => 'Admin/Analytics/Index',
            '/admin/audits'              => 'Admin/Audits/Index',
            '/admin/quotas'              => 'Admin/Quotas/Index',
            '/admin/system'              => 'Admin/System/Index',
            '/admin/templates'           => 'Admin/Templates/Index',
            '/admin/webhooks'            => 'Admin/Webhooks/Index',
            '/admin/users/create'        => 'Admin/Users/Form',
            '/admin/campuses'            => 'Admin/Erp/ResourceIndex',
            '/admin/departments'         => 'Admin/Erp/ResourceIndex',
            '/admin/batches'             => 'Admin/Erp/ResourceIndex',
            '/admin/employees'           => 'Admin/Erp/ResourceIndex',
            '/admin/challans'            => 'Admin/Erp/ResourceIndex',
            '/admin/library'             => 'Admin/Erp/ResourceIndex',
            '/submissions'               => 'Submissions/Queue',
            "/courses/{$course->slug}"   => 'Courses/Show',
        ];

        foreach ($routes as $uri => $component) {
            $this->actingAs($admin)
                ->get($uri)
                ->assertOk()
                ->assertInertia(fn (Assert $p) => $p->component($component));
        }
    }
}
