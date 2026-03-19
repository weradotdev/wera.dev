<?php

namespace App\Channels;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Sms
{
    public function send(User $notifiable, Notification $notification): void
    {
        /**
         * @var User $user
         */
        $message = $notification->toSms($notifiable);

        $phoneNumber = $notifiable->routeNotificationForSms();

        if (null === $phoneNumber) {
            Log::error('User does not have a phone number to send OTP codes', [
                'user_id' => $notifiable->id,
                'email'   => $notifiable->email,
            ]);

            return;
        }

        $recipient = $phoneNumber;
        try {
            $payload = [
                'ApiKey'            => config('services.onfon.sms.api_key'),
                'ClientId'          => config('services.onfon.sms.client_id'),
                'SenderId'          => config('services.onfon.sms.sender_id'),
                'MessageParameters' => [
                    [
                        'Number' => $recipient,
                        'Text'   => $message,
                    ],
                ],
            ];

            $response = Http::log()->baseUrl(config('services.onfon.sms.api_url', 'https://api.onfonmedia.co.ke/v1/sms'))
                ->asJson()
                ->withHeaders(['AccessKey' => config('services.onfon.sms.api_key')])
                ->retry(1, 100)
                ->post('SendBulkSMS', $payload);

            if ($response->successful()) {
                $res = $response->json();

                Log::info('OTP message sent successfully', [
                    'phone_number' => $recipient,
                    // 'type' => $type->value,
                    'json' => $res,
                ]);
            }

            $body = $response->body();
            Log::error('OTP SMS not sent. Early return', [
                'phone_number' => $recipient,
                // 'type' => $type->value,
                'response' => $body,
            ]);

        } catch (\Exception $exception) {
            Log::error('OTP code for not sent. Exception caught', [
                'phone_number' => $recipient,
                // 'type' => $type->value,
                'error' => $exception,
            ]);
        }
    }
}
