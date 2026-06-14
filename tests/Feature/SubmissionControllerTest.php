<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * SubmissionControllerTest — Tests the submission lifecycle:
 * start → submit → grade, with RBAC enforcement.
 */
class SubmissionControllerTest extends TestCase
{
    use RefreshDatabase;

    private function createEnrolledStudent(): array
    {
        $instructor = User::factory()->instructor()->create();
        $student    = User::factory()->student()->create();
        $course     = Course::factory()->create(['instructor_id' => $instructor->id]);

        $course->enrollments()->create([
            'student_id' => $student->id,
            'status'     => 'active',
        ]);

        $assignment = Assignment::factory()->create([
            'course_id'  => $course->id,
            'max_points' => 100,
            'due_date'   => now()->addDays(7),
        ]);

        return compact('instructor', 'student', 'course', 'assignment');
    }

    // ── Queue ─────────────────────────────────────────────────────────────

    public function test_instructor_can_view_grading_queue(): void
    {
        $data = $this->createEnrolledStudent();
        $this->actingAs($data['instructor'])
             ->get(route('submissions.queue'))
             ->assertOk();
    }

    public function test_students_cannot_view_grading_queue(): void
    {
        $student = User::factory()->student()->create();
        $this->actingAs($student)
             ->get(route('submissions.queue'))
             ->assertForbidden();
    }

    // ── Start ─────────────────────────────────────────────────────────────

    public function test_student_can_start_submission(): void
    {
        $data = $this->createEnrolledStudent();

        $response = $this->actingAs($data['student'])
                         ->post(route('submissions.start', $data['assignment']->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('submissions', [
            'student_id'    => $data['student']->id,
            'assignment_id' => $data['assignment']->id,
            'status'        => 'in_progress',
        ]);
    }

    public function test_student_cannot_start_duplicate_submission(): void
    {
        $data = $this->createEnrolledStudent();

        // Start first
        $this->actingAs($data['student'])
             ->post(route('submissions.start', $data['assignment']->id));

        // Try again
        $response = $this->actingAs($data['student'])
                         ->post(route('submissions.start', $data['assignment']->id));

        $response->assertRedirect();
        $this->assertEquals(1, Submission::where([
            'student_id'    => $data['student']->id,
            'assignment_id' => $data['assignment']->id,
        ])->count());
    }

    // ── Grade (uses PATCH) ────────────────────────────────────────────────

    public function test_instructor_can_grade_submission(): void
    {
        $data = $this->createEnrolledStudent();

        $submission = Submission::factory()->create([
            'student_id'    => $data['student']->id,
            'assignment_id' => $data['assignment']->id,
            'status'        => 'submitted',
            'submitted_at'  => now(),
        ]);

        $response = $this->actingAs($data['instructor'])
                         ->patch(route('submissions.grade', $submission->id), [
                             'grade'    => 85,
                             'feedback' => 'Good work!',
                         ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('submissions', [
            'id'     => $submission->id,
            'status' => 'graded',
            'grade'  => 85,
        ]);
    }

    public function test_grade_cannot_exceed_max_points(): void
    {
        $data = $this->createEnrolledStudent();

        $submission = Submission::factory()->create([
            'student_id'    => $data['student']->id,
            'assignment_id' => $data['assignment']->id,
            'status'        => 'submitted',
            'submitted_at'  => now(),
        ]);

        $response = $this->actingAs($data['instructor'])
                         ->patch(route('submissions.grade', $submission->id), [
                             'grade'    => 150, // exceeds 100
                             'feedback' => 'Invalid grade',
                         ]);

        $response->assertSessionHasErrors(['grade']);
    }

    public function test_student_cannot_grade_submission(): void
    {
        $data = $this->createEnrolledStudent();

        $submission = Submission::factory()->create([
            'student_id'    => $data['student']->id,
            'assignment_id' => $data['assignment']->id,
            'status'        => 'submitted',
        ]);

        $this->actingAs($data['student'])
             ->patch(route('submissions.grade', $submission->id), [
                 'grade'    => 100,
                 'feedback' => 'Self-grading attempt',
             ])
             ->assertForbidden();
    }
}
