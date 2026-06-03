<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CodeUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $roomId;

    public int $userId;

    public string $fileId;

    public string $content;

    public int $version;

    public function __construct(
        string $roomId,
        int $userId,
        string $fileId,
        string $content,
        int $version = 1
    ) {
        $this->roomId = $roomId;
        $this->userId = $userId;
        $this->fileId = $fileId;
        $this->content = $content;
        $this->version = $version;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('workspace.'.$this->roomId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'code.updated';
    }

    /**
     * Only broadcast to other users, not back to the sender.
     */
    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'file_id' => $this->fileId,
            'content' => $this->content,
            'version' => $this->version,
        ];
    }
}
