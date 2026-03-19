<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\Projects\ProjectResource;
use App\Models\Project;
use App\Models\Workspace;
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class AdminOngoingProjects extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Ongoing Projects';

    public function table(Table $table): Table
    {
        $tenant = Filament::getTenant();

        return $table
            ->query($this->getOngoingProjectsQuery($tenant))
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('boards_count')
                    ->label('Boards')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tasks_count')
                    ->label('Tasks')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('open_tasks_count')
                    ->label('Open Tasks')
                    ->numeric()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon('hugeicons-view')
                    ->url(fn(Project $record) => ProjectResource::getUrl('view', [
                        'tenant' => $record->workspace->slug,
                        'record' => $record->slug,
                    ])),
            ])
            ->defaultSort('updated_at', 'desc')
            ->paginated(false)
            ->searchable(false);
    }

    private function getOngoingProjectsQuery(mixed $tenant): Builder
    {
        if (!$tenant instanceof Workspace) {
            return Project::query()->whereRaw('1 = 0');
        }

        return Project::query()
            ->whereBelongsTo($tenant)
            ->whereIn('status', ['planning', 'active', 'on_hold'])
            ->withCount([
                'boards',
                'tasks',
                'tasks as open_tasks_count' => fn(Builder $query) => $query
                    ->whereHas('board', fn(Builder $boardQuery): Builder => $boardQuery->whereNotIn('name', ['Completed', 'Done'])),
            ]);
    }
}
