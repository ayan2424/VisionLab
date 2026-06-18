<?php

namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ChatMessage $message;
    public string $roomSlug;

    /**
     * Create a new event instance.
     */
    public function __construct(ChatMessage $message, string $roomSlug)
    {
        $this->message = $message;
        $this->roomSlug = $roomSlug;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('collab.' . $this->roomSlug),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id'         => $this->message->id,
            'user_id'    => $this->message->user_id,
            'user_name'  => $this->message->user->name ?? 'User',
            'message'    => $this->message->message,
            'created_at' => $this->message->created_at->toIso8601String(),
        ];
    }
}
