<?php

namespace Tests\Feature;

use App\Models\User;
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

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/broadcasting/auth', [
            'channel_name' => 'presence-workspace.1',
            'socket_id' => '12345.67890'
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['auth', 'channel_data']);
    }
}
