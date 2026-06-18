<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_cannot_access_another_students_workspace()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student1 = User::factory()->create(['role' => 'student']);
        $student2 = User::factory()->create(['role' => 'student']);

        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        $workspace1 = Workspace::factory()->create([
            'student_id' => $student1->id,
            'course_id'  => $course->id,
            'status'     => 'running'
        ]);

        $this->actingAs($student2);

        // Try to view student1's workspace
        $response = $this->get("/workspace/{$workspace1->id}");
        
        $response->assertStatus(403);
    }

    public function test_instructor_can_access_student_workspace_in_their_course()
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $student = User::factory()->create(['role' => 'student']);

        $course = Course::factory()->create(['instructor_id' => $instructor->id]);

        $workspace = Workspace::factory()->create([
            'student_id' => $student->id,
            'course_id'  => $course->id,
            'status'     => 'running'
        ]);

        $this->actingAs($instructor);

        $response = $this->get("/workspace/{$workspace->id}");
        
        $response->assertStatus(200);
    }
}
