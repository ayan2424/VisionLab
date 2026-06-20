<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ExtensionIntegrityFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $workspace;
    public $target;
    public $expectedChecksum;
    public $actualChecksum;

    /**
     * Create a new event instance.
     */
    public function __construct($workspace, $target, $expectedChecksum, $actualChecksum)
    {
        $this->workspace = $workspace;
        $this->target = $target;
        $this->expectedChecksum = $expectedChecksum;
        $this->actualChecksum = $actualChecksum;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
