<?php

namespace App\Filament\Admin\Widgets\Charts;

use App\Models\Task;
use App\Models\Workspace;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class WorkspaceTasksStatusChart extends ChartWidget
{
    protected ?string $heading = 'Tasks by status';

    protected ?string $description = 'Open vs completed in this workspace';

    protected ?string $maxHeight = '280px';

    protected ?string $pollingInterval = null;

    protected bool $isCollapsible = true;

    protected function getType(): string
    {
        return 'doughnut';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Workspace) {
            return [
                'datasets' => [['data' => [], 'backgroundColor' => []]],
                'labels'   => [],
            ];
        }

        $query = Task::query()->where('workspace_id', $tenant->getKey());
        $total = (clone $query)->count();

        $completed = (clone $query)
            ->whereHas('board', fn (Builder $q): Builder => $q->whereIn('name', ['Completed', 'Done']))
            ->count();

        $open = $total - $completed;

        return [
            'datasets' => [
                [
                    'data'            => [$open, $completed],
                    'backgroundColor' => ['rgb(59, 130, 246)', 'rgb(34, 197, 94)'],
                ],
            ],
            'labels' => ['Open', 'Completed'],
        ];
    }
}
