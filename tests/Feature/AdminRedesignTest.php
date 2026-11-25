<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AdminRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_renders_inertia_with_real_trend_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Dashboard')
                ->has('stats')
                ->has('signupTrend')
                ->has('aiActionsTrend.labels', 6)
                ->has('aiActionsTrend.counts', 6)
            );
    }

    public function test_admin_status_renders_inertia_status_component_with_probe_checks(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)
            ->get('/admin/status')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Status')
                ->has('checks.database')
                ->has('checks.redis')
                ->has('checks.storage')
            );
    }

    public function test_admin_governance_and_erp_routes_enforce_admin_role(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);

        $routes = [
            '/admin/users',
            '/admin/workspaces',
            '/admin/extensions',
            '/admin/audits',
            '/admin/campuses',
            '/admin/challans',
        ];

        foreach ($routes as $uri) {
            $this->actingAs($admin)->get($uri)->assertOk();
            $this->actingAs($student)->get($uri)->assertForbidden();
        }
    }
}
