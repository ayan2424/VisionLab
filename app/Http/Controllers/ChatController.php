<?php

namespace App\Http\Controllers;

use App\Models\Workspace;
use App\Models\ChatMessage;
use App\Events\ChatMessageSent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function store(Request $request, Workspace $workspace)
    {
        $this->authorize('access', $workspace);

        $validated = $request->validate([
            'message' => 'required|string|max:10000',
            'room_slug' => 'required|string',
        ]);

        $chat = ChatMessage::create([
            'workspace_id' => $workspace->id,
            'user_id'      => auth()->id(),
            'message'      => $validated['message'],
        ]);

        broadcast(new ChatMessageSent($chat, $validated['room_slug']));

        return response()->json([
            'status' => 'success',
            'message_id' => $chat->id,
        ]);
    }
}
