<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * EnrollmentControllerTest — Tests the enrollment flow
 * with code-based join, duplicate prevention, and RBAC.
 */
class EnrollmentControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_student_can_view_join_form(): void
    {
        $student = User::factory()->student()->create();
        $this->actingAs($student)->get(route('enrollments.join'))->assertOk();
    }

    public function test_student_can_join_course_with_valid_code(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student    = User::factory()->student()->create();
        $course     = Course::factory()->create([
            'instructor_id'   => $instructor->id,
            'enrollment_code' => 'ABC12345',
        ]);

        $response = $this->actingAs($student)->post(route('enrollments.join.post'), [
            'enrollment_code' => 'abc12345',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id'  => $course->id,
            'status'     => 'active',
        ]);
    }

    public function test_student_cannot_join_with_invalid_code(): void
    {
        $student = User::factory()->student()->create();

        $response = $this->actingAs($student)->post(route('enrollments.join.post'), [
            'enrollment_code' => 'INVALID8',
        ]);

        $response->assertSessionHasErrors();
    }

    public function test_student_cannot_enroll_twice(): void
    {
        $instructor = User::factory()->instructor()->create();
        $student    = User::factory()->student()->create();
        $course     = Course::factory()->create([
            'instructor_id'   => $instructor->id,
            'enrollment_code' => 'DUP00001',
        ]);

        // First enrollment
        $this->actingAs($student)->post(route('enrollments.join.post'), [
            'enrollment_code' => 'DUP00001',
        ]);

        // Second attempt
        $response = $this->actingAs($student)->post(route('enrollments.join.post'), [
            'enrollment_code' => 'DUP00001',
        ]);

        $response->assertRedirect();
        $this->assertEquals(1, Enrollment::where([
            'student_id' => $student->id,
            'course_id'  => $course->id,
        ])->count());
    }

    public function test_instructor_cannot_join_own_course(): void
    {
        $instructor = User::factory()->instructor()->create();
        $course     = Course::factory()->create([
            'instructor_id'   => $instructor->id,
            'enrollment_code' => 'OWN00001',
        ]);

        $response = $this->actingAs($instructor)->post(route('enrollments.join.post'), [
            'enrollment_code' => 'OWN00001',
        ]);

        // Should redirect with error or be handled gracefully
        $this->assertDatabaseMissing('enrollments', [
            'student_id' => $instructor->id,
            'course_id'  => $course->id,
        ]);
    }
}
