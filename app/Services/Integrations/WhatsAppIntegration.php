<?php

namespace App\Services\Integrations;

use App\Models\Project;
use Illuminate\Support\Facades\Http;

class WhatsAppIntegration
{
    public function send(Project $project, string $toPhone, string $message): bool
    {
        $to = $this->toJid($toPhone);

        return $this->sendToJid($project, $to, $message);
    }

    /**
     * Send a message to the project's WhatsApp group, e.g. to notify and mention assignees.
     *
     * @param array<int, string> $assigneeNames Names to mention in the message (e.g. assignee names)
     */
    public function sendToGroup(Project $project, string $message, array $assigneeNames = []): bool
    {
        $settings = $project->settings;
        if (empty($settings['whatsapp']['connected']) || empty($settings['whatsapp']['has_group'])) {
            return false;
        }
        $groupJid = trim($settings['whatsapp']['group_jid'] ?? '');
        if (blank($groupJid)) {
            return false;
        }
        if (! str_contains($groupJid, '@g.us')) {
            $groupJid .= '@g.us';
        }
        $body = $message;
        if ([] !== $assigneeNames) {
            $body .= "\n\nAssignees: ".implode(', ', $assigneeNames);
        }

        return $this->sendToJid($project, $groupJid, $body);
    }

    protected function sendToJid(Project $project, string $jid, string $message): bool
    {
        $settings = $project->settings;
        if (empty($settings['whatsapp']['connected'])) {
            return false;
        }
        $sessionId = $settings['whatsapp']['session_id'] ?? (string) $project->id;
        $baseUrl = rtrim(config('services.whatsapp.url', 'http://localhost:3000'), '/');

        $response = Http::timeout(15)->post("{$baseUrl}/send", [
            'session_id' => $sessionId ?: 'project-'.$project->id,
            'to'         => $jid,
            'message'    => $message,
        ]);

        return $response->successful();
    }

    protected function toJid(string $phoneOrJid): string
    {
        if (str_contains($phoneOrJid, '@')) {
            return $phoneOrJid;
        }
        $phone = preg_replace('/\D/', '', $phoneOrJid);

        return $phone.'@s.whatsapp.net';
    }
}
