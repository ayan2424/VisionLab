<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CursorMoved implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $roomId;

    public int $userId;

    public string $userName;

    public string $userColor;

    public string $fileId;

    public int $line;

    public int $column;

    public function __construct(
        string $roomId,
        int $userId,
        string $userName,
        string $userColor,
        string $fileId,
        int $line,
        int $column
    ) {
        $this->roomId = $roomId;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userColor = $userColor;
        $this->fileId = $fileId;
        $this->line = $line;
        $this->column = $column;
    }

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('workspace.'.$this->roomId),
        ];
    }

    public function broadcastAs(): string
    {
        return 'cursor.moved';
    }

    public function broadcastWith(): array
    {
        return [
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'user_color' => $this->userColor,
            'file_id' => $this->fileId,
            'line' => $this->line,
            'column' => $this->column,
        ];
    }
}
