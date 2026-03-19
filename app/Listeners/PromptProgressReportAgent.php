<?php

namespace App\Listeners;

use App\Ai\Agents\ProgressReportAgent;
use App\Events\GenerateProgressReportRequested;
use App\Events\ProgressReportGenerated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class PromptProgressReportAgent implements ShouldQueue
{
    public function handle(GenerateProgressReportRequested $event): void
    {
        $project = $event->project;

        try {
            $agent = new ProgressReportAgent($project);
            $prompt = 'Write a progress report for this project. Use the tools to fetch project id '.$project->id.', its tasks (with progress and assignees), and team members, then produce the report.';
            $response = $agent->prompt($prompt);
            $report = (string) $response;

            event(new ProgressReportGenerated($project, $report));
        } catch (Throwable $e) {
            Log::error('ProgressReportAgent failed: '.$e->getMessage(), [
                'project_id' => $project->id,
                'exception'  => $e,
            ]);
            throw $e;
        }
    }
}
