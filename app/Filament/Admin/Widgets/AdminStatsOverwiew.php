<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use Filament\Facades\Filament;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverwiew extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Workspace) {
            return [
                Stat::make('Projects', '0'),
                Stat::make('Ongoing Projects', '0'),
                Stat::make('Open Tasks', '0'),
                Stat::make('Completed Tasks', '0'),
            ];
        }

        $projectQuery = Project::query()->whereBelongsTo($tenant);

        $projectCount = (clone $projectQuery)->count();
        $ongoingProjectCount = (clone $projectQuery)
            ->whereIn('status', ['planning', 'active', 'on_hold'])
            ->count();

        $taskQuery = Task::query()->whereBelongsTo($tenant);

        $openTaskCount = (clone $taskQuery)
            ->whereHas('board', fn ($query) => $query->whereNotIn('name', ['Completed', 'Done']))
            ->count();

        $completedTaskCount = (clone $taskQuery)
            ->whereHas('board', fn ($query) => $query->whereIn('name', ['Completed', 'Done']))
            ->count();

        return [
            Stat::make('Projects', (string) $projectCount)
                ->description('All projects in this workspace'),
            Stat::make('Ongoing Projects', (string) $ongoingProjectCount)
                ->description('Planning, active, or on hold'),
            Stat::make('Open Tasks', (string) $openTaskCount)
                ->description('Not completed yet'),
            Stat::make('Completed Tasks', (string) $completedTaskCount)
                ->description('Done or completed'),
        ];
    }
}
