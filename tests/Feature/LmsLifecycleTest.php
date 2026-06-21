<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Assignment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LmsLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_instructor_can_create_course_and_assignment()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        
        $response = $this->actingAs($instructor)->post('/courses', [
            'title' => 'Web Development 101',
            'description' => 'Learn the basics of web development in this course.',
        ]);

        $response->assertStatus(302);
        
        $course = Course::where('title', 'Web Development 101')->firstOrFail();

        $assignmentResponse = $this->actingAs($instructor)->post("/courses/{$course->slug}/assignments", [
            'course_id' => $course->id,
            'title' => 'HTML Basics',
            'description' => 'Build a webpage using HTML tags.',
            'due_date' => now()->addDays(7)->toDateTimeString(),
            'max_score' => 100,
            'status' => 'published'
        ]);

        $assignmentResponse->assertStatus(302);

        $this->assertDatabaseHas('assignments', [
            'course_id' => $course->id,
            'title' => 'HTML Basics',
        ]);
    }
}
