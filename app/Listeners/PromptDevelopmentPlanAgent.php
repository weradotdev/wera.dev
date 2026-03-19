<?php

namespace App\Listeners;

use App\Ai\Agents\DevelopmentPlanAgent;
use App\Events\DevelopmentPlanGenerated;
use App\Events\GenerateDevelopmentPlanRequested;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

class PromptDevelopmentPlanAgent implements ShouldQueue
{
    public function handle(GenerateDevelopmentPlanRequested $event): void
    {
        $project = $event->project;
        $plan = $event->plan;

        try {
            $agent = new DevelopmentPlanAgent($project);
            $prompt = 'Generate a development plan for this project. Use the tools to fetch project id '.$project->id.' and its tasks and users, then produce a phased plan with suggested tasks.';
            $response = $agent->prompt($prompt);
            $planContent = (string) $response;

            event(new DevelopmentPlanGenerated($project, $planContent, $plan));
        } catch (Throwable $e) {
            Log::error('DevelopmentPlanAgent failed: '.$e->getMessage(), [
                'project_id' => $project->id,
                'plan_id'    => $plan->id,
                'exception'  => $e,
            ]);
            throw $e;
        }
    }
}
