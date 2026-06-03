<?php

namespace App\Events;

use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class VideoCallStarted implements ShouldBroadcastNow
{
    use Dispatchable;

    public function __construct(
        public string $workspaceSlug,
        public string $roomName,
        public string $jitsiDomain,
        public int $starterId,
        public string $starterName,
    ) {}

    public function broadcastOn(): array
    {
        return [new PresenceChannel('collab.'.$this->workspaceSlug)];
    }

    public function broadcastAs(): string
    {
        return 'video.started';
    }
}
