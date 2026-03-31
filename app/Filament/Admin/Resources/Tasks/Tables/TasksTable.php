<?php

namespace App\Filament\Admin\Resources\Tasks\Tables;

use App\Filament\Admin\Resources\Tasks\Schemas\TaskForm;
use App\Filament\Admin\Resources\Tasks\TaskResource;
use App\Models\Task;
use App\Models\User;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use CodeWithKyrian\FilamentDateRange\Tables\Filters\DateRangeFilter;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('project.name')
                    ->searchable(),
                TextColumn::make('board.name')
                    ->label('Status')
                    ->searchable(),
                TextColumn::make('assignedUsers.name')
                    ->label('Assigned')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->searchable(),
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
                DateRangeFilter::make('schedule')
                    ->label('Schedule')
                    ->withTime()
                    ->modifyQueryUsing(function (Builder $query, $start, $end): void {
                        if (!$start && !$end) {
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
                Action::make('assign')
                    ->label('Assign')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->schema(fn(Task $record): array => [
                        Repeater::make('assignments')
                            ->label('Assigned users')
                            ->schema([
                                Select::make('user_id')
                                    ->label('User')
                                    ->options(
                                        User::query()
                                            ->whereHas('workspaces', fn(Builder $q) => $q->where('workspaces.id', $record->workspace_id))
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Select::make('role')
                                    ->options(TaskForm::taskRoleOptions())
                                    ->required()
                                    ->default('developer'),
                            ])
                            ->columns(2)
                            ->default(fn() => $record->assignedUsers->map(fn($user) => [
                                'user_id' => $user->id,
                                'role'    => $user->pivot->role ?? 'developer',
                            ])->toArray()),
                        DateRangePicker::make('schedule')
                            ->label('Schedule')
                            ->withTime()
                            ->singleField()
                            ->format('Y-m-d H:i:s')
                            ->displayFormat('M j, Y H:i')
                            ->default(fn() => $record->start_at && $record->end_at
                                ? ['start' => $record->start_at->format('Y-m-d H:i:s'), 'end' => $record->end_at->format('Y-m-d H:i:s')]
                                : null),
                    ])
                    ->action(function (Task $record, array $data): void {
                        static::assignUsers($record, $data['assignments'] ?? [], $data['schedule'] ?? null);
                    })
                    ->successRedirectUrl(fn(Task $record) => TaskResource::getUrl('view', ['record' => $record])),
                ViewAction::make(),
                EditAction::make(),
                CommentsAction::make()
                    ->mentionables(User::query()->orderBy('name')->get())
                    ->slideOver()
                    ->modalWidth('lg'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Sync assigned users with roles and optionally update task schedule.
     *
     * @param array<int, array{user_id: int|string, role?: string}>     $assignments
     * @param array{start?: string|null, end?: string|null}|string|null $schedule
     */
    public static function assignUsers(Task $record, array $assignments, array|string|null $schedule = null): void
    {
        $sync = collect($assignments)
            ->filter(fn(array $a): bool => !empty($a['user_id']))
            ->mapWithKeys(fn(array $a): array => [(int) $a['user_id'] => ['role' => $a['role'] ?? 'developer']])
            ->all();

        $record->assignedUsers()->sync($sync);

        if (!empty($sync)) {
            app(\App\Services\TaskIntegrationService::class)->notifyNewAssignees($record, array_keys($sync));
        }

        $startAt = static::parseScheduleStart($schedule);
        $endAt   = static::parseScheduleEnd($schedule);
        if (null !== $startAt || null !== $endAt) {
            $record->update(array_filter([
                'start_at' => $startAt,
                'end_at'   => $endAt,
            ]));
        }
    }

    /**
     * @param array{start?: string|null, end?: string|null}|string|null $schedule
     */
    private static function parseScheduleStart(array|string|null $schedule): ?Carbon
    {
        if (null === $schedule) {
            return null;
        }

        $start = is_array($schedule) ? ($schedule['start'] ?? null) : $schedule;

        return filled($start) ? Carbon::parse($start) : null;
    }

    /**
     * @param array{start?: string|null, end?: string|null}|string|null $schedule
     */
    private static function parseScheduleEnd(array|string|null $schedule): ?Carbon
    {
        if (null === $schedule || !is_array($schedule)) {
            return null;
        }

        $end = $schedule['end'] ?? null;

        return filled($end) ? Carbon::parse($end) : null;
    }
}
