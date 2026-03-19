<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\Charts\MyTaskProgressChart;
use App\Filament\Widgets\Charts\MyTasksOverTimeChart;
use App\Filament\Widgets\Charts\MyTasksStatusChart;
use BackedEnum;
use Filament\Pages\Dashboard;

class Analytics extends Dashboard
{
    protected static string $routePath = 'analytics';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'Analytics';

    protected static ?string $title = 'Analytics';

    protected static ?int $navigationSort = 10;

    /**
     * @return array<class-string<\Filament\Widgets\Widget>|\Filament\Widgets\WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            MyTasksOverTimeChart::class,
            MyTasksStatusChart::class,
            MyTaskProgressChart::class,
        ];
    }
}
