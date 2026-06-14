<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AdminControllerTest — Tests admin dashboard access control
 * and user management operations.
 */
class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    // ── Dashboard Access ──────────────────────────────────────────────────

    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }

    public function test_instructor_cannot_access_admin_dashboard(): void
    {
        $instructor = User::factory()->instructor()->create();
        $this->actingAs($instructor)->get(route('admin.dashboard'))->assertForbidden();
    }

    public function test_student_cannot_access_admin_dashboard(): void
    {
        $student = User::factory()->student()->create();
        $this->actingAs($student)->get(route('admin.dashboard'))->assertForbidden();
    }

    // ── User Management ──────────────────────────────────────────────────

    public function test_admin_can_list_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->student()->count(5)->create();

        $this->actingAs($admin)
             ->get(route('admin.users.index'))
             ->assertOk()
             ->assertSee('student');
    }

    public function test_admin_can_search_users(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->student()->create(['name' => 'Unique Searchable Name']);
        User::factory()->student()->count(3)->create();

        $this->actingAs($admin)
             ->get(route('admin.users.index', ['search' => 'Unique Searchable']))
             ->assertOk()
             ->assertSee('Unique Searchable Name');
    }

    public function test_admin_can_filter_by_role(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->instructor()->count(2)->create();
        User::factory()->student()->count(3)->create();

        $this->actingAs($admin)
             ->get(route('admin.users.index', ['role' => 'instructor']))
             ->assertOk();
    }

    public function test_admin_can_suspend_user(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->student()->create();

        $this->actingAs($admin)
             ->post(route('admin.users.suspend', $target->id))
             ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id'     => $target->id,
            'status' => 'suspended',
        ]);
    }

    public function test_admin_cannot_suspend_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->post(route('admin.users.suspend', $admin->id))
             ->assertSessionHasErrors();
    }

    public function test_admin_can_activate_suspended_user(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->suspended()->create();

        $this->actingAs($admin)
             ->post(route('admin.users.activate', $target->id))
             ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id'     => $target->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_update_user_role(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->student()->create();

        $this->actingAs($admin)
             ->put(route('admin.users.update', $target->id), [
                 'name'   => $target->name,
                 'email'  => $target->email,
                 'role'   => 'instructor',
                 'status' => 'active',
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('users', [
            'id'   => $target->id,
            'role' => 'instructor',
        ]);
    }

    public function test_admin_can_delete_user(): void
    {
        $admin  = User::factory()->admin()->create();
        $target = User::factory()->student()->create();

        $this->actingAs($admin)
             ->delete(route('admin.users.destroy', $target->id))
             ->assertRedirect();

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_admin_cannot_delete_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->delete(route('admin.users.destroy', $admin->id))
             ->assertSessionHasErrors();
    }

    // ── Analytics ─────────────────────────────────────────────────────────

    public function test_admin_can_access_analytics(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('admin.analytics'))->assertOk();
    }

    // ── Extension Management ──────────────────────────────────────────────

    public function test_admin_can_list_extensions(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get(route('admin.extensions.index'))->assertOk();
    }
}
