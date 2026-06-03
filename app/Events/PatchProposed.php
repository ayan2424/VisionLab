<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PatchProposed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $roomSlug;

    public array $patchData;

    /**
     * Create a new event instance.
     */
    public function __construct(string $roomSlug, array $patchData)
    {
        $this->roomSlug = $roomSlug;
        $this->patchData = $patchData;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new PresenceChannel('collab.'.$this->roomSlug),
            new PrivateChannel('workspace.'.$this->roomSlug.'.patches'),
        ];
    }

    /**
     * Data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'patch' => $this->patchData,
        ];
    }
}
