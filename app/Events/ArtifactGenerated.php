<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ArtifactGenerated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $workspaceSlug,
        public string $title,
        public string $language,
        public string $content,
        public int $userId,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel("workspace.{$this->workspaceSlug}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'artifact.generated';
    }

    public function broadcastWith(): array
    {
        return [
            'title'    => $this->title,
            'language' => $this->language,
            'content'  => $this->content,
            'user_id'  => $this->userId,
        ];
    }
}
