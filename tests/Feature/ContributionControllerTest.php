<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ContributionControllerTest — Tests the 365-day heatmap API.
 */
class ContributionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_fetch_own_heatmap(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)
                         ->getJson(route('contributions.heatmap'));

        $response->assertOk()
                 ->assertJsonStructure([
                     'heatmap',
                     'current_streak',
                     'max_streak',
                     'total_contributions',
                 ]);

        $this->assertCount(366, $response->json('heatmap'));
    }

    public function test_heatmap_days_have_correct_structure(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)
                         ->getJson(route('contributions.heatmap'));

        $first = $response->json('heatmap.0');
        $this->assertArrayHasKey('date', $first);
        $this->assertArrayHasKey('count', $first);
        $this->assertArrayHasKey('submissions', $first);
        $this->assertArrayHasKey('ai_actions', $first);
        $this->assertArrayHasKey('level', $first);
    }

    public function test_non_admin_cannot_view_other_users_heatmap(): void
    {
        $student = User::factory()->student()->create();
        $other   = User::factory()->student()->create();

        $response = $this->actingAs($student)
                         ->getJson(route('contributions.heatmap', ['user_id' => $other->id]));

        $response->assertForbidden();
    }

    public function test_admin_can_view_other_users_heatmap(): void
    {
        $admin   = User::factory()->admin()->create();
        $student = User::factory()->student()->create();

        $response = $this->actingAs($admin)
                         ->getJson(route('contributions.heatmap', ['user_id' => $student->id]));

        $response->assertOk();
    }

    public function test_guest_cannot_access_heatmap(): void
    {
        $this->getJson(route('contributions.heatmap'))
             ->assertUnauthorized();
    }
}
