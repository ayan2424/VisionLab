<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewAnnouncement implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public int $courseId,
        public string $courseTitle,
        public string $title,
        public string $authorName,
        public string $preview,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PresenceChannel("course.{$this->courseId}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'announcement.new';
    }

    public function broadcastWith(): array
    {
        return [
            'course_id'    => $this->courseId,
            'course_title' => $this->courseTitle,
            'title'        => $this->title,
            'author_name'  => $this->authorName,
            'preview'      => $this->preview,
        ];
    }
}
