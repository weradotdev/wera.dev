<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\BoardsKanbanWidget;
use App\Filament\Widgets\TasksCalendarWidget;
use BackedEnum;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static string|BackedEnum|null $navigationIcon = 'hugeicons-calendar-02';

    protected static ?string $navigationLabel = 'Home';

    protected static string $routePath = '/account';

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            BoardsKanbanWidget::class,
            TasksCalendarWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $user = auth()->user();

        return "Welcome back, {$user->first_name}!";
    }
}
