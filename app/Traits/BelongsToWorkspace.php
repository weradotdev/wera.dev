<?php

namespace App\Traits;

use App\Models\Project;
use App\Models\Workspace;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToWorkspace
{
    protected static function bootBelongsToWorkspace(): void
    {
        static::creating(static function ($model): void {
            if (0 === $model->workspace_id || '0' === $model->workspace_id) {
                $model->workspace_id = null;

                return;
            }

            if (filled($model->workspace_id)) {
                return;
            }

            $workspace = Filament::getTenant();

            match (true) {
                $workspace instanceof Workspace => $model->workspace_id = $workspace->getKey(),
                $workspace instanceof Project   => $model->workspace_id = $workspace->workspace_id,
                default                         => null,
            };
        });
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
