<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;

class TaskInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('description')
                ->hiddenLabel()
                    ->columnSpanFull(),
                        TextEntry::make('project.name')
                            ->label('Project'),
                        TextEntry::make('board.name')
                            ->label('Status'),
                        TextEntry::make('assignedUsers.name')
                            ->label('Assigned')
                            ->listWithLineBreaks(),
                        TextEntry::make('priority')
                            ->badge(),
                        TextEntry::make('due_at')
                            ->dateTime(),
                        TextEntry::make('start_at')
                            ->dateTime(),
                        TextEntry::make('end_at')
                            ->dateTime(),
                            CommentsEntry::make('comments')
                    ->mentionables(User::query()->orderBy('name')->get())
                    ->columnSpanFull(),
            ])
            ->columns(5);
    }
}
