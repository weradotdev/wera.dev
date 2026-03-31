<?php

use App\Models\Project;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

new class extends Component {
    public string $sessionId;

    public ?int $projectId = null;

    public ?string $qrDataUrl = null;

    public bool $connected = false;

    public string $statusMessage = '';

    public bool $loading = true;

    public function mount(string $sessionId, ?int $projectId = null): void
    {
        $this->sessionId = $sessionId;
        $this->projectId = $projectId;
        $this->fetchQr();
    }

    public function setConnectionState(?string $qr, bool $connected): void
    {
        $this->qrDataUrl     = $qr;
        $this->connected     = $connected;
        $this->statusMessage = $connected ? 'Connected' : ($qr ? 'Scan with your phone' : 'Waiting for QR…');
        $this->loading       = false;
        
        if ($connected) {
            $this->markProjectConnected();
        }
    }

    public function fetchQr(): void
    {
        if ($this->connected) {
            return;
        }

        $this->loading = true;

        $baseUrl       = rtrim(config('services.whatsapp.url', 'http://localhost:3000'), '/');

        try {
            $response = Http::log()
            ->timeout(10)
                ->get($baseUrl . '/qr', [
                    'session_id' => $this->sessionId,
                ]);

            if (!$response->successful()) {
                $this->statusMessage = 'Could not reach WhatsApp service.';
                $this->loading       = false;

                return;
            }

            $data                = $response->json();
            $this->connected     = (bool) ($data['connected'] ?? false);
            $this->qrDataUrl     = $data['qr'] ?? null;
            $this->statusMessage = $data['message'] ?? ($this->connected ? 'Connected' : ($this->qrDataUrl ? 'Open WhatsApp on your phone, tap Menu → Linked devices → Link a device, then scan the QR code.' : 'Waiting for QR...'));
            if ($this->connected) {
                $this->markProjectConnected();
            }
        }
        catch (\Throwable $e) {
            $this->statusMessage = 'Error: ' . $e->getMessage();
            $this->qrDataUrl     = null;
        }

        $this->loading = false;
    }

    protected function markProjectConnected(): void
    {
        if ($this->projectId === null) {
            return;
        }

        $project = Project::query()->find($this->projectId);
        if (!$project) {
            return;
        }

        $settings                           = $project->settings;
        $settings['whatsapp']['connected']  = true;
        $settings['whatsapp']['session_id'] = $this->sessionId;
        $project->update(['settings' => $settings]);
    }
}

?>

<div class="space-y-4" wire:poll.2s="fetchQr" x-data="{ sessionId: @js($sessionId) }" x-init="
        if (window.Echo) {
            window.Echo.channel('whatsapp.session.' + sessionId)
                .listen('.connection.update', (e) => {
                    $wire.setConnectionState(e.qr || null, e.connected || false);
                    console.info('ECHO', e)
                });
        }
    ">
    
    @if($loading && !$qrDataUrl && !$connected)
        <p class="text-sm text-gray-500 dark:text-gray-400">Connecting to WhatsApp service…</p>
    @elseif($connected)
        <div
            class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800 dark:border-green-800 dark:bg-green-500/10 dark:text-green-400">
            <p class="font-medium">Connected</p>
            <p class="text-sm">WhatsApp is linked. You can close this and save the project.</p>
        </div>
    @elseif($qrDataUrl)
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $statusMessage }}</p>
        <div class="flex justify-center">
            <img src="{{ $qrDataUrl }}" alt="WhatsApp QR code"
                class="max-w-70 rounded mt-20" />
        </div>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $statusMessage ?: 'Waiting for QR…' }}</p>
    @endif
</div>
