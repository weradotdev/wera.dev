<?php

namespace App\Services\Integrations;

use App\Models\Project;
use Illuminate\Support\Facades\Http;

class SlackIntegration
{
    public function send(Project $project, string $message, ?string $channel = null): bool
    {
        $settings = $project->settings;
        if (empty($settings['slack']['connected'])) {
            return false;
        }
        $webhookUrl = $settings['slack']['webhook_url'] ?? '';
        if (blank($webhookUrl)) {
            return false;
        }

        $payload = ['text' => $message];
        if (filled($channel)) {
            $payload['channel'] = $channel;
        } elseif (filled($settings['slack']['channel'] ?? null)) {
            $payload['channel'] = $settings['slack']['channel'];
        }

        $response = Http::post($webhookUrl, $payload);

        return $response->successful();
    }
}
