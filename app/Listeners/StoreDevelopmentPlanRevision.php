<?php

namespace App\Listeners;

use App\Events\DevelopmentPlanGenerated;
use App\Models\PlanRevision;

class StoreDevelopmentPlanRevision
{
    public function handle(DevelopmentPlanGenerated $event): void
    {
        PlanRevision::query()->create([
            'plan_id'     => $event->plan->id,
            'name'        => $event->plan->name.' — AI Generated',
            'description' => $event->planContent,
        ]);
    }
}
