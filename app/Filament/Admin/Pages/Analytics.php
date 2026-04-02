<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Widgets\Charts\TasksByPriorityChart;
use App\Filament\Admin\Widgets\Charts\TopUsersChart;
use App\Filament\Admin\Widgets\Charts\UserPerformanceChart;
use App\Filament\Admin\Widgets\Charts\WorkspaceTasksOverTimeChart;
use App\Filament\Admin\Widgets\Charts\WorkspaceTasksStatusChart;
use BackedEnum;
use Filament\Pages\Dashboard;
use Filament\Widgets\Widget;
use Filament\Widgets\WidgetConfiguration;

class Analytics extends Dashboard
{
    protected static string $routePath = 'analytics';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 7;

    protected static ?string $navigationLabel = 'Analytics';

    protected static ?string $title = 'Analytics';

    /**
     * @return array<class-string<Widget>|WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            WorkspaceTasksOverTimeChart::class,
            WorkspaceTasksStatusChart::class,
            TasksByPriorityChart::class,
            UserPerformanceChart::class,
            TopUsersChart::class,
        ];
    }
}
