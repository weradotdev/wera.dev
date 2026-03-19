<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WhatsAppConnectionUpdate implements ShouldBroadcast
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public string $sessionId,
        public ?string $qr = null,
        public bool $connected = false,
    ) {}

    /**
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('whatsapp.session.'.$this->sessionId),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'qr'        => $this->qr,
            'connected' => $this->connected,
        ];
    }

    public function broadcastAs(): string
    {
        return 'connection.update';
    }
}
