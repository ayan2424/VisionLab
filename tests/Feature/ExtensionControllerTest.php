<?php

namespace Tests\Feature;

use App\Models\Extension;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ExtensionControllerTest — Verifies administrative operations on extensions,
 * including validation rules, built-in safeguards, and role authorizations.
 */
class ExtensionControllerTest extends TestCase
{
    use RefreshDatabase;

    // ── Admin Authorization Checks ────────────────────────────────────────

    public function test_admin_can_access_extension_management(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('admin.extensions.index'));

        $response->assertOk()
                 ->assertViewIs('admin.extensions.index');
    }

    public function test_instructor_cannot_access_extension_management(): void
    {
        $instructor = User::factory()->instructor()->create();

        $response = $this->actingAs($instructor)->get(route('admin.extensions.index'));

        $response->assertForbidden();
    }

    public function test_student_cannot_access_extension_management(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get(route('admin.extensions.index'));

        $response->assertForbidden();
    }

    // ── Extension Registry CRUD Operations ────────────────────────────────

    public function test_admin_can_store_new_extension(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('admin.extensions.store'), [
            'name'               => 'Vim Keybindings',
            'package_identifier' => 'vscode-vim',
            'version'            => '1.0.0',
            'description'        => 'Vim keybindings wrapper',
            'is_global'          => true,
        ]);

        $response->assertRedirect(route('admin.extensions.index'))
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('extensions', [
            'package_identifier' => 'vscode-vim',
            'is_global'          => true,
            'is_builtin'         => false,
            'is_active'          => true,
        ]);
    }

    public function test_store_extension_validates_unique_identifier(): void
    {
        $admin = User::factory()->admin()->create();
        Extension::factory()->create(['package_identifier' => 'vscode-vim']);

        $response = $this->actingAs($admin)->post(route('admin.extensions.store'), [
            'name'               => 'Vim Keybindings Duplicate',
            'package_identifier' => 'vscode-vim',
            'version'            => '1.0.1',
        ]);

        $response->assertSessionHasErrors('package_identifier');
    }

    public function test_admin_can_update_extension(): void
    {
        $admin = User::factory()->admin()->create();
        $extension = Extension::factory()->create([
            'name'               => 'Old Name',
            'package_identifier' => 'test-extension',
            'version'            => '1.0.0',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.extensions.update', $extension->id), [
            'name'        => 'New Name',
            'version'     => '1.1.0',
            'description' => 'Updated description',
            'is_global'   => true,
        ]);

        $response->assertRedirect(route('admin.extensions.index'))
                 ->assertSessionHas('success');

        $this->assertDatabaseHas('extensions', [
            'id'          => $extension->id,
            'name'        => 'New Name',
            'version'     => '1.1.0',
            'is_global'   => true,
        ]);
    }

    public function test_admin_can_toggle_extension_active_state(): void
    {
        $admin = User::factory()->admin()->create();
        $extension = Extension::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)->patch(route('admin.extensions.toggle', $extension->id));

        $response->assertOk()
                 ->assertJsonPath('is_active', false);

        $this->assertFalse($extension->fresh()->is_active);
    }

    public function test_admin_can_delete_custom_extension(): void
    {
        $admin = User::factory()->admin()->create();
        $extension = Extension::factory()->create(['is_builtin' => false]);

        $response = $this->actingAs($admin)->delete(route('admin.extensions.destroy', $extension->id));

        $response->assertRedirect(route('admin.extensions.index'))
                 ->assertSessionHas('success');

        $this->assertModelMissing($extension);
    }

    public function test_admin_cannot_delete_builtin_extension(): void
    {
        $admin = User::factory()->admin()->create();
        $extension = Extension::factory()->create(['is_builtin' => true]);

        $response = $this->actingAs($admin)->delete(route('admin.extensions.destroy', $extension->id));

        $response->assertSessionHasErrors('error');
        $this->assertModelExists($extension);
    }
}
