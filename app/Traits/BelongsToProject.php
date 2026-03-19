<?php

namespace App\Traits;

use App\Models\Project;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToProject
{
    protected static function bootBelongsToProject(): void
    {
        static::creating(static function ($model): void {
            if (blank($model->project_id)) {
                $tenant = Filament::getTenant();

                if ($tenant instanceof Project) {
                    $model->project_id = $tenant->getKey();
                }
            }

            if (blank($model->workspace_id) && filled($model->project_id)) {
                $project = Project::query()
                    ->select(['id', 'workspace_id'])
                    ->find($model->project_id);

                $model->workspace_id = $project?->workspace_id;
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
