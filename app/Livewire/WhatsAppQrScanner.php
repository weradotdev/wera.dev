<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class WhatsAppQrScanner extends Component
{
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
        $this->qrDataUrl = $qr;
        $this->connected = $connected;
        $this->statusMessage = $connected ? 'Connected' : ($qr ? 'Scan with your phone' : 'Waiting for QR…');
        $this->loading = false;
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
        $baseUrl = rtrim(config('services.whatsapp.url', 'http://localhost:3000'), '/');

        try {
            $response = Http::timeout(10)->get($baseUrl.'/qr', [
                'session_id' => $this->sessionId,
            ]);

            if (! $response->successful()) {
                $this->statusMessage = 'Could not reach WhatsApp service.';
                $this->loading = false;

                return;
            }

            $data = $response->json();
            $this->connected = (bool) ($data['connected'] ?? false);
            $this->qrDataUrl = $data['qr'] ?? null;
            $this->statusMessage = $data['message'] ?? ($this->connected ? 'Connected' : ($this->qrDataUrl ? 'Scan with your phone' : 'Waiting for QR...'));
            if ($this->connected) {
                $this->markProjectConnected();
            }
        } catch (\Throwable $e) {
            $this->statusMessage = 'Error: '.$e->getMessage();
            $this->qrDataUrl = null;
        }

        $this->loading = false;
    }

    protected function markProjectConnected(): void
    {
        if ($this->projectId === null) {
            return;
        }

        $project = Project::query()->find($this->projectId);
        if (! $project) {
            return;
        }

        $settings = $project->settings;
        $settings['whatsapp']['connected'] = true;
        $settings['whatsapp']['session_id'] = $this->sessionId;
        $project->update(['settings' => $settings]);
    }

    public function render()
    {
        return view('livewire.whats-app-qr-scanner');
    }
}
