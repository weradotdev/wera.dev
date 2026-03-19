<?php

namespace App\Events;

use App\Models\Plan;
use App\Models\Project;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DevelopmentPlanGenerated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Project $project,
        public string $planContent,
        public Plan $plan
    ) {}
}
