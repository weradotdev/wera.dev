<?php

namespace App\Filament\Resources\Tasks\Tables;

use App\Models\User;
use CodeWithKyrian\FilamentDateRange\Tables\Filters\DateRangeFilter;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('workspace.name')
                    ->label('Workspace')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('board.name')
                    ->label('Status')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priority')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'low' => 'gray',
                        'medium' => 'warning',
                        'high' => 'danger',
                    }),
                TextColumn::make('due_at')
                ->label('Due date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('start_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('end_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('position')
                    ->numeric()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnManagerLayout(ColumnManagerLayout::Modal)
            ->columnManagerTriggerAction(fn($action) => $action->slideOver())
            ->filters([
                SelectFilter::make('priority')
                    ->options([
                        'low'    => 'Low',
                        'medium' => 'Medium',
                        'high'   => 'High',
                    ]),
                DateRangeFilter::make('schedule')
                    ->label('Schedule')
                    ->withTime()
                    ->modifyQueryUsing(function (Builder $query, $start, $end): void {
                        if (! $start && ! $end) {
                            return;
                        }

                        $query->where(function (Builder $builder) use ($start, $end): void {
                            if ($start && $end) {
                                $builder
                                    ->where('start_at', '<=', $end)
                                    ->where(function (Builder $inner) use ($start): void {
                                        $inner
                                            ->where('end_at', '>=', $start)
                                            ->orWhereNull('end_at');
                                    });

                                return;
                            }

                            if ($start) {
                                $builder->where(function (Builder $inner) use ($start): void {
                                    $inner
                                        ->where('end_at', '>=', $start)
                                        ->orWhereNull('end_at');
                                });

                                return;
                            }

                            $builder->where('start_at', '<=', $end);
                        });
                    }),
            ])
            ->recordActions([
                CommentsAction::make()
                    ->mentionables(User::query()->orderBy('name')->get()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
