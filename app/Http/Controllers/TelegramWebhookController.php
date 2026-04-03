<?php

namespace App\Http\Controllers;

use App\Services\TelegramCommandHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    /**
     * Handle an incoming Telegram webhook update.
     *
     * The route is secured by validating the X-Telegram-Bot-Api-Secret-Token header
     * against the TELEGRAM_WEBHOOK_SECRET environment variable.
     */
    public function __invoke(Request $request, TelegramCommandHandler $handler): JsonResponse
    {
        $secret = config('services.telegram.webhook_secret');

        if (filled($secret) && $request->header('X-Telegram-Bot-Api-Secret-Token') !== $secret) {
            abort(403, 'Invalid Telegram webhook secret.');
        }

        /** @var array<string, mixed> $update */
        $update = $request->json()->all();

        if (! is_array($update)) {
            return response()->json(['ok' => false], 400);
        }

        $handler->handle($update);

        return response()->json(['ok' => true]);
    }
}
