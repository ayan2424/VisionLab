<?php

namespace App\Http\Controllers;

use App\Events\UserJoinedRoom;
use App\Models\Room;
use App\Models\RoomMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    /**
     * Create a new collaborative room and redirect to it.
     * POST /rooms
     */
    public function create(\App\Http\Requests\StoreRoomRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $room = Room::create([
            'slug'     => Room::generateSlug(),
            'name'     => $validated['name'],
            'owner_id' => Auth::id(),
            'language' => $validated['language'] ?? 'python',
        ]);

        return redirect()->route('workspace.show', ['workspace' => $room->slug]);
    }

    /**
     * Join a room and broadcast presence.
     * POST /rooms/{slug}/join
     */
    public function join(Request $request, string $slug): JsonResponse
    {
        $room = Room::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        // Add to members if not already present
        RoomMember::firstOrCreate(
            ['room_id' => $room->id, 'user_id' => $user->id],
            ['joined_at' => now()]
        );

        // Broadcast join event
        broadcast(new UserJoinedRoom($room->slug, [
            'id'       => $user->id,
            'name'     => $user->name,
            'initials' => $user->avatar_initials,
            'color'    => $room->getPresenceColor($user->id),
            'role'     => $user->role,
        ]))->toOthers();

        return response()->json([
            'success'    => true,
            'room_slug'  => $room->slug,
            'room_name'  => $room->name,
            'user_color' => $room->getPresenceColor($user->id),
        ]);
    }

    /**
     * Broadcast a code change to all room members.
     * POST /rooms/{slug}/broadcast
     */
    public function broadcastCode(\App\Http\Requests\BroadcastCodeRequest $request, string $slug): JsonResponse
    {
        $validated = $request->validated();

        $room = Room::where('slug', $slug)->firstOrFail();

        if (! $room->isMember(Auth::user())) {
            return response()->json(['error' => 'Not a room member'], 403);
        }

        broadcast(new \App\Events\CodeUpdated(
            roomId:  $room->slug,
            userId:  Auth::id(),
            fileId:  $validated['file_id'],
            content: $validated['content'],
            version: $validated['version'] ?? 1,
        ))->toOthers();

        return response()->json(['success' => true]);
    }

    /**
     * Broadcast cursor position to all room members.
     * POST /rooms/{slug}/cursor
     */
    public function broadcastCursor(\App\Http\Requests\BroadcastCursorRequest $request, string $slug): JsonResponse
    {
        $validated = $request->validated();

        $room = Room::where('slug', $slug)->firstOrFail();
        $user = Auth::user();

        broadcast(new \App\Events\CursorMoved(
            roomId:    $room->slug,
            userId:    $user->id,
            userName:  $user->name,
            userColor: $room->getPresenceColor($user->id),
            fileId:    $validated['file_id'],
            line:      $validated['line'],
            column:    $validated['column'],
        ))->toOthers();

        return response()->json(['success' => true]);
    }
}
