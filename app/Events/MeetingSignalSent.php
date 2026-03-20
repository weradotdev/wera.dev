<?php

namespace App\Events;

use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeetingSignalSent implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    /**
     * @param array<string, mixed> $payload
     */
    public function __construct(
        public string $meetingId,
        public int $fromUserId,
        public string $type,
        public array $payload,
        public ?int $toUserId = null,
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('meetings.'.$this->meetingId),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'meeting_id'   => $this->meetingId,
            'from_user_id' => $this->fromUserId,
            'to_user_id'   => $this->toUserId,
            'type'         => $this->type,
            'payload'      => $this->payload,
            'sent_at'      => now()->toIso8601String(),
        ];
    }

    public function broadcastAs(): string
    {
        return 'meeting.signal';
    }
}
