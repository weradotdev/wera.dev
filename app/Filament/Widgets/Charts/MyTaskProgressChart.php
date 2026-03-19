<?php

namespace App\Filament\Widgets\Charts;

use App\Models\Task;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MyTaskProgressChart extends ChartWidget
{
    protected ?string $heading = 'Checklist progress';

    protected ?string $description = 'Average completion of your tasks with checklists';

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected bool $isCollapsible = true;

    protected function getType(): string
    {
        return 'bar';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $tenant = Filament::getTenant();

        $query = Task::query()
            ->whereHas('assignedUsers', fn (Builder $q) => $q->where('users.id', auth()->id()))
            ->whereNotNull('checklist')
            ->where('checklist', '!=', '[]');

        if ($tenant && method_exists($tenant, 'getKey')) {
            $query->where('project_id', $tenant->getKey());
        }

        $tasks = $query->get();

        if ($tasks->isEmpty()) {
            return [
                'datasets' => [['label' => 'Progress %', 'data' => []]],
                'labels'   => [],
            ];
        }

        $progressByMonth = $tasks->groupBy(fn (Task $t) => $t->created_at->format('Y-m'))
            ->map(fn (Collection $group): float => $group->avg('progress'))
            ->sortKeys();

        $labels = $progressByMonth->keys()->map(fn (string $m) => Carbon::createFromFormat('Y-m', $m)->format('M Y'))->values()->all();

        return [
            'datasets' => [
                [
                    'label' => 'Avg progress %',
                    'data'  => $progressByMonth->values()->map(fn (float $v) => round($v, 1))->values()->all(),
                ],
            ],
            'labels' => $labels,
        ];
    }
}
