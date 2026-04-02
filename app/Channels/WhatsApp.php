<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsApp
{
    /**
     * @param object $notifiable Must have routeNotificationFor('whatsapp') and the notification must implement toWhatsApp()
     */
    public function send(object $notifiable, Notification $notification): void
    {
        $message = $notification->toWhatsApp($notifiable);

        $phoneNumber = $notifiable->routeNotificationFor('whatsapp', $notification);

        if (blank($phoneNumber)) {
            Log::warning('WhatsApp notification skipped: no phone number', [
                'notifiable_type' => $notifiable::class,
                'notifiable_id'   => $notifiable->getKey(),
            ]);

            return;
        }

        $sessionId = config('services.whatsapp.default_session_id');
        if (blank($sessionId)) {
            Log::warning('WhatsApp notification skipped: no default_session_id in config');

            return;
        }

        $jid = str_contains($phoneNumber, '@')
            ? $phoneNumber
            : preg_replace('/\D/', '', $phoneNumber).'@s.whatsapp.net';

        $baseUrl = rtrim(config('services.whatsapp.url', 'http://localhost:3000'), '/');

        try {
            $response = Http::timeout(15)
                ->post("{$baseUrl}/send", [
                    'session_id' => $sessionId,
                    'to'         => $jid,
                    'message'    => $message,
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent', [
                    'to' => $jid,
                ]);

                return;
            }

            Log::error('WhatsApp message not sent', [
                'to'       => $jid,
                'status'   => $response->status(),
                'response' => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error('WhatsApp notification exception', [
                'to'    => $jid,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
