<?php

namespace Tests\Feature;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use App\Models\Submission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Illuminate\Support\Str;

class CourseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_show_performance()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::create([
            'instructor_id' => $instructor->id,
            'title' => 'Test Course',
            'slug' => 'test-course-' . Str::random(4),
            'enrollment_code' => Str::random(6),
        ]);
        $course->students()->attach($student->id, ['status' => 'active']);

        for ($i = 0; $i < 10; $i++) {
            $assignment = Assignment::create([
                'course_id' => $course->id,
                'title' => 'Test Assignment ' . $i,
                'max_points' => 100,
            ]);

            Submission::create([
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'status' => 'submitted',
            ]);
        }

        DB::enableQueryLog();

        $response = $this->actingAs($student)->get(route('courses.show', $course->slug));

        $response->assertStatus(200);

        $queries = DB::getQueryLog();

        $submissionQueries = collect($queries)->filter(function ($query) {
            return str_contains($query['query'], 'select * from "submissions"');
        })->count();

        // Output the number of queries for submissions
        dump("Submission queries: " . $submissionQueries);

        $this->assertLessThan(10, $submissionQueries, 'Too many queries fetching submissions (N+1 issue)');
    }
}
