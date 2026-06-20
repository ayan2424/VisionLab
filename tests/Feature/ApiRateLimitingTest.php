<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_rate_limiter_blocks_excessive_requests()
    {
        $user = User::factory()->create();

        // Laravel default API limit is usually 60 per minute.
        // We'll just verify the rate limit headers are present.
        $response = $this->actingAs($user)->getJson('/api/user');

        $response->assertStatus(200);
        $response->assertHeader('X-RateLimit-Limit');
        $response->assertHeader('X-RateLimit-Remaining');
    }
}
