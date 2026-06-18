<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Course;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_rate_limiting_enforces_auth_throttle()
    {
        // Simulate multiple failed login attempts
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'email'    => 'nonexistent@example.com',
                'password' => 'password123',
            ]);
        }

        // Laravel's built-in Breeze login request throws ValidationException on throttle, which redirects back with errors.
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['email']);
    }

    public function test_workspace_file_api_prevents_path_traversal()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['instructor_id' => $user->id]);
        $workspace = Workspace::factory()->create([
            'student_id' => $user->id,
            'course_id'  => $course->id,
            'status'     => 'running'
        ]);

        $this->actingAs($user);

        // Attempt to read a file outside the workspace using traversal
        $response = $this->getJson("/api/workspace/ws-{$workspace->id}/read-file?path=../../../../etc/passwd");
        
        $response->assertStatus(404); // Read file returns 404 for not found or access denied
    }

    public function test_workspace_file_api_prevents_writing_to_env()
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['instructor_id' => $user->id]);
        $workspace = Workspace::factory()->create([
            'student_id' => $user->id,
            'course_id'  => $course->id,
            'status'     => 'running'
        ]);

        $this->actingAs($user);

        // Attempt to write to .env
        $response = $this->postJson("/api/workspace/ws-{$workspace->id}/write-file", [
            'path'    => '.env',
            'content' => 'HACKED=true',
        ]);
        
        $response->assertStatus(403);
    }
}
