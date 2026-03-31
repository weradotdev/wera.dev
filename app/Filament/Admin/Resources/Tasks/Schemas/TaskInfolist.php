<?php

namespace App\Filament\Admin\Resources\Tasks\Schemas;

use App\Filament\Admin\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
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
                    ->label('Project')
                    ->url(fn(Task $record) => ProjectResource::getUrl('view', [
                        'tenant' => $record->workspace->slug,
                        'record' => $record->project->slug,
                    ])),
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
                    ->dateTime()
                    ->placeholder('N/A'),
                TextEntry::make('end_at')
                    ->dateTime()
                    ->placeholder('N/A'),
                CommentsEntry::make('comments')
                    ->mentionables(fn(Model $record) => User::query()->orderBy('name')->get())
                    ->columnSpanFull(),
            ])
            ->columns(5);
    }
}
