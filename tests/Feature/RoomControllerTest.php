<?php

namespace Tests\Feature;

use App\Events\CodeUpdated;
use App\Events\CursorMoved;
use App\Events\UserJoinedRoom;
use App\Models\Room;
use App\Models\RoomMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * RoomControllerTest — Verifies WebSocket-enabled collaboration rooms,
 * membership restrictions, and event broadcasting.
 */
class RoomControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_room(): void
    {
        $user = User::factory()->student()->create();

        $response = $this->actingAs($user)->post(route('rooms.create'), [
            'name'     => 'Dev Room',
            'language' => 'javascript',
        ]);

        // Note: Controller redirects to route('workspace.show', ['workspace' => $room->slug])
        // Let's assert a redirect occurred
        $response->assertRedirect();
        
        $this->assertDatabaseHas('rooms', [
            'name'     => 'Dev Room',
            'owner_id' => $user->id,
            'language' => 'javascript',
        ]);
    }

    public function test_user_can_join_room_and_trigger_broadcast(): void
    {
        Event::fake([UserJoinedRoom::class]);

        $owner = User::factory()->student()->create();
        $guest = User::factory()->student()->create();

        $room = Room::create([
            'slug'     => Room::generateSlug(),
            'name'     => 'Collab Hub',
            'owner_id' => $owner->id,
            'language' => 'python',
        ]);

        $response = $this->actingAs($guest)->post(route('api.rooms.join', $room->slug));

        $response->assertOk()
                 ->assertJsonPath('success', true);

        $this->assertDatabaseHas('room_members', [
            'room_id' => $room->id,
            'user_id' => $guest->id,
        ]);

        Event::assertDispatched(UserJoinedRoom::class, function (UserJoinedRoom $event) use ($room, $guest) {
            return $event->roomId === $room->slug && $event->userInfo['id'] === $guest->id;
        });
    }

    public function test_member_can_broadcast_code_update(): void
    {
        Event::fake([CodeUpdated::class]);

        $owner = User::factory()->student()->create();
        $room = Room::create([
            'slug'     => Room::generateSlug(),
            'name'     => 'Collab Hub',
            'owner_id' => $owner->id,
            'language' => 'python',
        ]);

        $response = $this->actingAs($owner)->post(route('api.rooms.broadcast', $room->slug), [
            'file_id' => 'src/main.py',
            'content' => 'print("Collab")',
            'version' => 3,
        ]);

        $response->assertOk()
                 ->assertJsonPath('success', true);

        Event::assertDispatched(CodeUpdated::class, function (CodeUpdated $event) use ($room, $owner) {
            return $event->roomId === $room->slug
                && $event->userId === $owner->id
                && $event->fileId === 'src/main.py'
                && $event->content === 'print("Collab")'
                && $event->version === 3;
        });
    }

    public function test_non_member_cannot_broadcast_code_update(): void
    {
        Event::fake([CodeUpdated::class]);

        $owner = User::factory()->student()->create();
        $stranger = User::factory()->student()->create();

        $room = Room::create([
            'slug'     => Room::generateSlug(),
            'name'     => 'Collab Hub',
            'owner_id' => $owner->id,
            'language' => 'python',
        ]);

        $response = $this->actingAs($stranger)->post(route('api.rooms.broadcast', $room->slug), [
            'file_id' => 'src/main.py',
            'content' => 'print("Malicious")',
        ]);

        $response->assertForbidden();
        Event::assertNotDispatched(CodeUpdated::class);
    }

    public function test_member_can_broadcast_cursor_position(): void
    {
        Event::fake([CursorMoved::class]);

        $owner = User::factory()->student()->create();
        $room = Room::create([
            'slug'     => Room::generateSlug(),
            'name'     => 'Collab Hub',
            'owner_id' => $owner->id,
            'language' => 'python',
        ]);

        $response = $this->actingAs($owner)->post(route('api.rooms.cursor', $room->slug), [
            'file_id' => 'src/main.py',
            'line'    => 10,
            'column'  => 5,
        ]);

        $response->assertOk()
                 ->assertJsonPath('success', true);

        Event::assertDispatched(CursorMoved::class, function (CursorMoved $event) use ($room, $owner) {
            return $event->roomId === $room->slug
                && $event->userId === $owner->id
                && $event->fileId === 'src/main.py'
                && $event->line === 10
                && $event->column === 5;
        });
    }
}
