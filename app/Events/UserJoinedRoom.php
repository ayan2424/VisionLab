<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserJoinedRoom implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $roomId;

    public array $userInfo;

    public function __construct(string $roomId, array $userInfo)
    {
        $this->roomId = $roomId;
        $this->userInfo = $userInfo;
    }

    public function broadcastOn(): array
    {
        return [new PresenceChannel('workspace.'.$this->roomId)];
    }

    public function broadcastAs(): string
    {
        return 'user.joined';
    }

    public function broadcastWith(): array
    {
        return $this->userInfo;
    }
}
