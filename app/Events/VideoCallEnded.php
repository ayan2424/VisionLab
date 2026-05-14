<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class VideoCallEnded implements ShouldBroadcastNow
{
    use Dispatchable;

    public function __construct(
        public string $workspaceSlug,
    ) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel('collab.' . $this->workspaceSlug)];
    }

    public function broadcastAs(): string
    {
        return 'video.ended';
    }
}
