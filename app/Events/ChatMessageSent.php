<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $workspaceSlug,
        public int $userId,
        public string $userName,
        public string $message,
        public string $timestamp,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel("workspace.{$this->workspaceSlug}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'chat.message';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'message' => $this->message,
            'timestamp' => $this->timestamp,
        ];
    }
}
