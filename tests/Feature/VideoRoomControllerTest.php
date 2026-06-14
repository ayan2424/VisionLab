<?php

namespace Tests\Feature;

use App\Events\VideoCallEnded;
use App\Events\VideoCallStarted;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * VideoRoomControllerTest — Verifies Jitsi Meet video call integrations,
 * room caching lifecycles, and event broadcasting.
 */
class VideoRoomControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_start_video_call(): void
    {
        Event::fake([VideoCallStarted::class]);

        $user = User::factory()->student()->create();
        $room = Room::create([
            'slug'     => Room::generateSlug(),
            'name'     => 'Collab Sandbox',
            'owner_id' => $user->id,
            'language' => 'python',
        ]);

        $response = $this->actingAs($user)->post(route('api.video.start', $room->slug));

        $response->assertOk()
                 ->assertJsonPath('active', true)
                 ->assertJsonStructure(['room_name', 'jitsi_domain', 'starter', 'jwt']);

        $data = $response->json();
        $this->assertStringContainsString('visioncode-' . $room->slug, $data['room_name']);
        $this->assertEquals('meet.jit.si', $data['jitsi_domain']);
        $this->assertEquals($user->id, $data['starter']['id']);

        // Public Jitsi, no JWT is returned
        $this->assertNull($data['jwt']);

        // Check call cached
        $this->assertTrue(Cache::has("video_call:{$room->slug}"));

        Event::assertDispatched(VideoCallStarted::class, function (VideoCallStarted $event) use ($room, $user, $data) {
            return $event->workspaceSlug === $room->slug
                && $event->roomName === $data['room_name']
                && $event->starterId === $user->id;
        });
    }

    public function test_subsequent_calls_return_cached_video_session(): void
    {
        $user1 = User::factory()->student()->create();
        $user2 = User::factory()->student()->create();
        $room = Room::create([
            'slug'     => Room::generateSlug(),
            'name'     => 'Collab Sandbox',
            'owner_id' => $user1->id,
            'language' => 'python',
        ]);

        // Start call with User 1
        $res1 = $this->actingAs($user1)->post(route('api.video.start', $room->slug));
        $res1->assertOk();
        $roomName = $res1->json('room_name');

        // Join call with User 2
        $res2 = $this->actingAs($user2)->post(route('api.video.start', $room->slug));
        $res2->assertOk()
             ->assertJsonPath('room_name', $roomName)
             ->assertJsonPath('starter.id', $user1->id); // Original starter remains User 1
    }

    public function test_authenticated_user_can_get_video_call_status(): void
    {
        $user = User::factory()->student()->create();
        $room = Room::create([
            'slug'     => Room::generateSlug(),
            'name'     => 'Collab Sandbox',
            'owner_id' => $user->id,
            'language' => 'python',
        ]);

        // 1. Status when call is inactive
        $response = $this->actingAs($user)->get(route('api.video.status', $room->slug));
        $response->assertOk()
                 ->assertJsonPath('active', false);

        // 2. Status when call is active
        $this->actingAs($user)->post(route('api.video.start', $room->slug))->assertOk();

        $response = $this->actingAs($user)->get(route('api.video.status', $room->slug));
        $response->assertOk()
                 ->assertJsonPath('active', true)
                 ->assertJsonStructure(['call' => ['room_name', 'jitsi_domain', 'starter', 'started_at']]);
    }

    public function test_jitsi_jwt_is_generated_when_auth_keys_are_configured(): void
    {
        config([
            'visionlab.jitsi.app_id'     => 'my-jitsi-app',
            'visionlab.jitsi.jwt_secret' => 'my-jitsi-secret-key',
        ]);

        $user = User::factory()->student()->create();
        $room = Room::create([
            'slug'     => Room::generateSlug(),
            'name'     => 'Collab Sandbox',
            'owner_id' => $user->id,
            'language' => 'python',
        ]);

        $response = $this->actingAs($user)->post(route('api.video.start', $room->slug));
        $response->assertOk();

        $jwt = $response->json('jwt');
        $this->assertNotNull($jwt);

        $parts = explode('.', $jwt);
        $this->assertCount(3, $parts); // Header.Payload.Signature
    }

    public function test_only_authorized_users_can_end_video_call(): void
    {
        Event::fake([VideoCallEnded::class]);

        $starter = User::factory()->student()->create();
        $otherStudent = User::factory()->student()->create();
        $instructor = User::factory()->instructor()->create();
        $admin = User::factory()->admin()->create();

        $room = Room::create([
            'slug'     => Room::generateSlug(),
            'name'     => 'Collab Sandbox',
            'owner_id' => $starter->id,
            'language' => 'python',
        ]);

        // Start call as student starter
        $this->actingAs($starter)->post(route('api.video.start', $room->slug))->assertOk();

        // 1. Other student tries to end call (should fail)
        $response = $this->actingAs($otherStudent)->post(route('api.video.end', $room->slug));
        $response->assertStatus(403);
        $this->assertTrue(Cache::has("video_call:{$room->slug}"));

        // 2. Starter student ends call (should succeed)
        $response = $this->actingAs($starter)->post(route('api.video.end', $room->slug));
        $response->assertOk()->assertJsonPath('success', true);
        $this->assertFalse(Cache::has("video_call:{$room->slug}"));
        Event::assertDispatched(VideoCallEnded::class, fn($e) => $e->workspaceSlug === $room->slug);

        // Restart call
        $this->actingAs($starter)->post(route('api.video.start', $room->slug))->assertOk();

        // 3. Instructor ends call (should succeed)
        $response = $this->actingAs($instructor)->post(route('api.video.end', $room->slug));
        $response->assertOk();
        $this->assertFalse(Cache::has("video_call:{$room->slug}"));

        // Restart call
        $this->actingAs($starter)->post(route('api.video.start', $room->slug))->assertOk();

        // 4. Admin ends call (should succeed)
        $response = $this->actingAs($admin)->post(route('api.video.end', $room->slug));
        $response->assertOk();
        $this->assertFalse(Cache::has("video_call:{$room->slug}"));
    }
}
