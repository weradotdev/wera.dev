<?php

namespace App\Filament\Admin\Resources\Tickets\Schemas;

use App\Filament\Admin\Resources\Tasks\TaskResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class TicketInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('description')
                ->hiddenLabel()
                    ->columnSpanFull(),
                TextEntry::make('workspace.name')
                    ->label('Workspace'),
                TextEntry::make('project.name')
                    ->label('Project'),
                TextEntry::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'     => 'gray',
                        'assigned' => 'warning',
                        'closed'   => 'success',
                        default    => 'gray',
                    }),
                TextEntry::make('task.id')
                    ->label('Task')
                    ->formatStateUsing(fn ($state, $record) => $state ? "#{$state}" : '—')
                    ->url(fn ($state, $record) => $state && $record->task ? TaskResource::getUrl('edit', ['record' => $record->task]) : null)
                    ->visible(fn ($record) => null !== $record->task),
                TextEntry::make('task.assignedUsers')
                    ->label('Assigned users')
                    ->formatStateUsing(fn ($state) => $state?->pluck('name')->join(', ') ?? '—')
                    ->visible(fn ($record) => $record->task?->assignedUsers->isNotEmpty()),
            ])
            ->columns(6);
    }
}
