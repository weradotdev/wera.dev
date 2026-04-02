<?php

namespace App\Filament\Admin\Widgets\Charts;

use App\Models\Task;
use App\Models\Workspace;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class WorkspaceTasksOverTimeChart extends ChartWidget
{
    public ?string $filter = '12months';

    protected ?string $cachedFilter = null;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Tasks over time';

    protected ?string $description = 'Tasks created and completed in this workspace, by month';

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected bool $isCollapsible = true;

    protected function getType(): string
    {
        return 'line';
    }

    /**
     * @return array<scalar, scalar>|null
     */
    protected function getFilters(): ?array
    {
        return [
            '6months'  => 'Last 6 months',
            '12months' => 'Last 12 months',
            'year'     => 'This year',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getCachedData(): array
    {
        if (null !== $this->cachedData && $this->filter !== $this->cachedFilter) {
            $this->cachedData = null;
        }
        $this->cachedFilter = $this->filter;

        return parent::getCachedData();
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    private function getFilterDateRange(): array
    {
        $end = now()->endOfMonth();

        $start = match ($this->filter) {
            '6months' => now()->subMonths(5)->startOfMonth(),
            'year'    => now()->startOfYear(),
            default   => now()->subMonths(11)->startOfMonth(),
        };

        return [$start, $end];
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

        $query = Task::query()->where('workspace_id', $tenant->getKey());
        [$start, $end] = $this->getFilterDateRange();

        $createdData = Trend::query($query->clone())
            ->between($start, $end)
            ->perMonth()
            ->count();

        $completedData = Trend::query(
            $query->clone()->whereHas('board', fn (Builder $q): Builder => $q->whereIn('name', ['Completed', 'Done']))
        )
            ->dateColumn('updated_at')
            ->between($start, $end)
            ->perMonth()
            ->count();

        return [
            'datasets' => [
                [
                    'label' => 'Tasks created',
                    'data'  => $createdData->map(fn (TrendValue $v) => (int) $v->aggregate)->values()->all(),
                ],
                [
                    'label' => 'Tasks completed',
                    'data'  => $completedData->map(fn (TrendValue $v) => (int) $v->aggregate)->values()->all(),
                ],
            ],
            'labels' => $createdData->map(fn (TrendValue $v) => Carbon::createFromFormat('Y-m', $v->date)->format('M Y'))->values()->all(),
        ];
    }
}
