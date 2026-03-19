<?php

namespace App\Services\Integrations;

use App\Models\Project;
use Illuminate\Support\Facades\Http;

class TelegramIntegration
{
    /**
     * Send a message to a Telegram chat. Callers (e.g. notifications) should pass
     * the correct chat_id for the target; chat_id is configured at project level in settings.
     */
    public function send(Project $project, string $chatId, string $message): bool
    {
        $settings = $project->settings;
        if (empty($settings['telegram']['connected'])) {
            return false;
        }
        $token = $settings['telegram']['bot_token'] ?? '';
        if (blank($token)) {
            return false;
        }

        $response = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id'    => $chatId,
            'text'       => $message,
            'parse_mode' => 'HTML',
        ]);

        return $response->successful();
    }
}
