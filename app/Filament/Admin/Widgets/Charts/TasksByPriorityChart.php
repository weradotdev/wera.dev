<?php

namespace App\Filament\Admin\Widgets\Charts;

use App\Models\Task;
use App\Models\Workspace;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;

class TasksByPriorityChart extends ChartWidget
{
    protected ?string $heading = 'Tasks by priority';

    protected ?string $description = 'Task count per priority in this workspace';

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

        if (! $tenant instanceof Workspace) {
            return [
                'datasets' => [],
                'labels'   => [],
            ];
        }

        $counts = Task::query()
            ->where('workspace_id', $tenant->getKey())
            ->selectRaw('priority, count(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');

        $order = ['low' => 'Low', 'medium' => 'Medium', 'high' => 'High'];
        $labels = [];
        $data = [];
        foreach ($order as $key => $label) {
            $labels[] = $label;
            $data[] = (int) ($counts[$key] ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => 'Tasks',
                    'data'  => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
