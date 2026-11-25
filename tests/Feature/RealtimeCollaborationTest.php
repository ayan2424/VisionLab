<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RealtimeCollaborationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_auth_reverb_presence_channel()
    {
        config([
            'broadcasting.default' => 'pusher',
            'broadcasting.connections.pusher.key' => '1234567890abcdef1234',
            'broadcasting.connections.pusher.secret' => '1234567890abcdef1234',
            'broadcasting.connections.pusher.app_id' => '12345',
        ]);

        $owner = User::factory()->student()->create();
        $intruder = User::factory()->student()->create();
        $workspace = Workspace::factory()->create(['student_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson('/broadcasting/auth', [
            'channel_name' => 'presence-workspace.' . $workspace->id,
            'socket_id' => '12345.67890',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['auth', 'channel_data']);

        $denied = $this->actingAs($intruder)->postJson('/broadcasting/auth', [
            'channel_name' => 'presence-workspace.' . $workspace->id,
            'socket_id' => '12345.67890',
        ]);

        $denied->assertStatus(403);
    }
}
