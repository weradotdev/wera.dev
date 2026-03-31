<?php

namespace App\Filament\Admin\Pages;

use App\Filament\Admin\Resources\Tickets\TicketResource;
use App\Filament\Admin\Widgets\AdminOngoingProjects;
use App\Filament\Admin\Widgets\AdminStatsOverwiew;
use App\Filament\Widgets\TasksCalendarWidget;
use BackedEnum;
use Filament\Actions\Action;

class Dashboard extends \Filament\Pages\Dashboard
{
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = 1;

    /**
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            AdminStatsOverwiew::class,
            AdminOngoingProjects::class,
            TasksCalendarWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createTicket')
                ->label('Create Ticket')
                ->icon('heroicon-o-ticket')
                ->color('primary')
                ->url(TicketResource::getUrl('create')),
        ];
    }

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $user = auth()->user();

        return "Welcome back, {$user->first_name}!";
    }

    public function getSubheading(): string|\Illuminate\Contracts\Support\Htmlable|null
    {
        $ws = filament()->getTenant();

        return "Manage {$ws->name} workspace and stay on top of your projects.";
    }
}
