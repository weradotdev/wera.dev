<div
    class="space-y-4"
    wire:poll.2s="fetchQr"
    x-data="{ sessionId: @js($sessionId) }"
    x-init="
        if (window.Echo) {
            window.Echo.channel('whatsapp.session.' + sessionId)
                .listen('.connection.update', (e) => {
                    $wire.setConnectionState(e.qr || null, e.connected || false);
                });
        }
    "
>
    @if($loading && !$qrDataUrl && !$connected)
        <p class="text-sm text-gray-500 dark:text-gray-400">Connecting to WhatsApp service…</p>
    @elseif($connected)
        <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-800 dark:border-green-800 dark:bg-green-500/10 dark:text-green-400">
            <p class="font-medium">Connected</p>
            <p class="text-sm">WhatsApp is linked. You can close this and save the project.</p>
        </div>
    @elseif($qrDataUrl)
        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $statusMessage }}</p>
        <div class="flex justify-center">
            <img src="{{ $qrDataUrl }}" alt="WhatsApp QR code" class="max-w-70 rounded border border-gray-200 dark:border-gray-600" />
        </div>
    @else
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $statusMessage ?: 'Waiting for QR…' }}</p>
    @endif
</div>
