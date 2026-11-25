<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\AnnouncementRead;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class OperateShellRedesignTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_inertia_auth_login_without_register_link(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Auth/Login')
            ->has('canResetPassword')
        );
        $response->assertDontSee('btn-glow', false);
        $response->assertDontSee('#B026FF', false);
        $response->assertDontSee('href="' . url('/register') . '"', false);
    }

    public function test_student_dashboard_shares_live_global_announcements_count_via_inertia(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $student = User::factory()->create(['role' => 'student']);

        $announcement = Announcement::create([
            'course_id' => null,
            'author_id' => $admin->id,
            'title'     => 'Fall Term Container Upgrade',
            'body'      => 'All Nix workspace profiles upgraded.',
            'pinned'    => true,
        ]);

        $this->actingAs($student)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard/Student')
                ->where('unreadAnnouncementsCount', 1)
            );

        AnnouncementRead::create([
            'announcement_id' => $announcement->id,
            'user_id'         => $student->id,
            'read_at'         => now(),
        ]);

        $this->actingAs($student)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard/Student')
                ->where('unreadAnnouncementsCount', 0)
            );
    }

    public function test_instructor_dashboard_renders_inertia_instructor_component(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);

        $this->actingAs($instructor)
            ->get('/dashboard')
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Dashboard/Instructor')
                ->has('pendingGrading')
                ->has('courses')
            );
    }
}
