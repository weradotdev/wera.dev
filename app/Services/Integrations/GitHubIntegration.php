<?php

namespace App\Services\Integrations;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Support\Facades\Http;

class GitHubIntegration
{
    public function createIssueForTask(Project $project, Task $task): bool
    {
        $settings = $project->settings;
        if (empty($settings['github']['connected']) || empty($settings['github']['create_issues_with_tasks'])) {
            return false;
        }
        $repoUrl = $settings['github']['repo_url'] ?? '';
        if (blank($repoUrl)) {
            return false;
        }
        $token = config('services.github.token');
        if (blank($token)) {
            return false;
        }
        $ownerRepo = $this->parseRepoFromUrl($repoUrl);
        if (null === $ownerRepo) {
            return false;
        }

        $response = Http::withToken($token)
            ->accept('application/vnd.github+json')
            ->post("https://api.github.com/repos/{$ownerRepo}/issues", [
                'title' => $task->title,
                'body'  => $this->issueBody($task),
            ]);

        return $response->successful();
    }

    /**
     * @return string|null e.g. "owner/repo"
     */
    protected function parseRepoFromUrl(string $url): ?string
    {
        $parsed = parse_url($url);
        $path = trim($parsed['path'] ?? '', '/');
        if ('' === $path) {
            return null;
        }
        if (preg_match('#^([^/]+)/([^/]+?)(?:\.git)?$#', $path, $m)) {
            return $m[1].'/'.$m[2];
        }

        return null;
    }

    protected function issueBody(Task $task): string
    {
        $parts = [];
        if (filled($task->description)) {
            $parts[] = $task->description;
        }
        $parts[] = '---';
        $parts[] = 'Created from Wera task #'.$task->id;

        return implode("\n\n", $parts);
    }
}
