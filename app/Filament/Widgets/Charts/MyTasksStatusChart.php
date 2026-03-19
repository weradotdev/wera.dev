<?php

namespace App\Filament\Widgets\Charts;

use App\Models\Task;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;

class MyTasksStatusChart extends ChartWidget
{
    protected ?string $heading = 'My tasks by status';

    protected ?string $description = 'Open vs completed';

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

        $query = Task::query()
            ->whereHas('assignedUsers', fn (Builder $q) => $q->where('users.id', auth()->id()));

        if ($tenant && method_exists($tenant, 'getKey')) {
            $query->where('project_id', $tenant->getKey());
        }

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
