<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * DashboardTest — Tests role-based dashboard routing and data availability.
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_sees_student_dashboard(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get(route('dashboard'));

        $response->assertOk()
                 ->assertViewIs('dashboard.student')
                 ->assertSee('Welcome back');
    }

    public function test_instructor_sees_instructor_dashboard(): void
    {
        $instructor = User::factory()->instructor()->create();

        $response = $this->actingAs($instructor)->get(route('dashboard'));

        $response->assertOk()
                 ->assertViewIs('dashboard.instructor');
    }

    public function test_admin_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
             ->get(route('dashboard'))
             ->assertRedirect(route('admin.dashboard'));
    }

    public function test_guest_redirects_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    public function test_student_dashboard_has_required_data(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->get(route('dashboard'));

        $response->assertViewHasAll([
            'courses',
            'upcomingAssignments',
            'recentAnnouncements',
            'pendingSubmissions',
            'streak',
        ]);
    }

    public function test_instructor_dashboard_has_required_data(): void
    {
        $instructor = User::factory()->instructor()->create();

        $response = $this->actingAs($instructor)->get(route('dashboard'));

        $response->assertViewHasAll([
            'courses',
            'pendingGrading',
            'totalStudents',
            'recentSubmissions',
            'lateSubmissions',
            'avgGrade',
            'pendingPatches',
        ]);
    }

    public function test_progress_page_accessible_to_students(): void
    {
        $student = User::factory()->student()->create();
        $this->actingAs($student)->get(route('progress.index'))->assertOk();
    }

    public function test_profile_page_accessible(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('profile.edit'))->assertOk();
    }
}
