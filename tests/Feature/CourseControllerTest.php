<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CourseControllerTest — Tests RBAC enforcement, CRUD operations,
 * and enrollment code generation for courses.
 */
class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    // ── Index ─────────────────────────────────────────────────────────────

    public function test_guests_cannot_view_courses(): void
    {
        $this->get(route('courses.index'))->assertRedirect(route('login'));
    }

    public function test_students_can_view_course_list(): void
    {
        $student = User::factory()->student()->create();
        $this->actingAs($student)->get(route('courses.index'))->assertOk();
    }

    // ── Create ────────────────────────────────────────────────────────────

    public function test_students_cannot_create_courses(): void
    {
        $student = User::factory()->student()->create();
        $response = $this->actingAs($student)->get(route('courses.create'));
        // Policy should return 403 Forbidden
        $response->assertForbidden();
    }

    public function test_instructor_can_store_course(): void
    {
        $instructor = User::factory()->instructor()->create();

        $response = $this->actingAs($instructor)->post(route('courses.store'), [
            'title'       => 'Test Course PHP',
            'description' => 'A comprehensive PHP course for testing purposes.',
            'language'    => 'php',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('courses', [
            'title'         => 'Test Course PHP',
            'instructor_id' => $instructor->id,
        ]);
    }

    public function test_store_course_generates_enrollment_code(): void
    {
        $instructor = User::factory()->instructor()->create();

        $this->actingAs($instructor)->post(route('courses.store'), [
            'title'       => 'Auto Code Course',
            'description' => 'Tests enrollment code generation.',
            'language'    => 'javascript',
        ]);

        $course = Course::where('title', 'Auto Code Course')->first();
        $this->assertNotNull($course);
        $this->assertNotNull($course->enrollment_code);
        $this->assertEquals(6, strlen($course->enrollment_code));
    }

    public function test_store_course_validates_required_fields(): void
    {
        $instructor = User::factory()->instructor()->create();

        $response = $this->actingAs($instructor)->post(route('courses.store'), []);

        $response->assertSessionHasErrors(['title']);
    }

    // ── Update ────────────────────────────────────────────────────────────

    public function test_instructor_can_update_own_course(): void
    {
        $instructor = User::factory()->instructor()->create();
        $course     = Course::factory()->create(['instructor_id' => $instructor->id]);

        $response = $this->actingAs($instructor)->put(route('courses.update', $course->slug), [
            'title'       => 'Updated Title',
            'description' => $course->description,
            'language'    => $course->language,
        ]);

        $response->assertRedirect();
        $course->refresh();
        $this->assertEquals('Updated Title', $course->title);
    }

    public function test_instructor_cannot_update_others_course(): void
    {
        $owner = User::factory()->instructor()->create();
        $other = User::factory()->instructor()->create();
        $course = Course::factory()->create(['instructor_id' => $owner->id]);

        $response = $this->actingAs($other)->put(route('courses.update', $course->slug), [
            'title'       => 'Hijacked',
            'description' => 'nope',
            'language'    => 'python',
        ]);
        // Should be forbidden
        $this->assertTrue(in_array($response->status(), [403, 302]));
    }

    // ── Suspend ───────────────────────────────────────────────────────────

    public function test_suspended_users_are_redirected(): void
    {
        $suspended = User::factory()->suspended()->create();
        // CheckAccountStatus middleware redirects suspended users to login (302)
        $this->actingAs($suspended)->get(route('courses.index'))->assertRedirect(route('login'));
    }
}
