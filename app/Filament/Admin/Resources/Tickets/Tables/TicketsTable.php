<?php

namespace App\Filament\Admin\Resources\Tickets\Tables;

use App\Filament\Admin\Resources\Tickets\TicketResource;
use App\Models\Task;
use App\Models\Ticket;
use App\Models\User;
use App\Services\TaskIntegrationService;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class TicketsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'open'     => 'gray',
                        'assigned' => 'warning',
                        'closed'   => 'success',
                        default    => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('task.assignedUsers.name')
                    ->label('Assigned')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList()
                    ->visible(fn (?Ticket $record) => $record?->task?->assignedUsers->isNotEmpty()),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->columnManagerLayout(ColumnManagerLayout::Modal)
            ->columnManagerTriggerAction(fn ($action) => $action->slideOver())
            ->recordActions([
                Action::make('assign')
                    ->label('Assign to users')
                    ->icon('heroicon-o-user-plus')
                    ->color('primary')
                    ->visible(fn (?Ticket $record) => $record && in_array($record->status, ['open', 'assigned'], true))
                    ->schema(fn (Ticket $record): array => [
                        Select::make('user_ids')
                            ->label('Users')
                            ->options(
                                User::query()
                                    ->whereHas('workspaces', fn (Builder $q) => $q->where('workspaces.id', $record->workspace_id))
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->multiple()
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(fn () => $record->task?->assignedUsers->pluck('id')->all() ?? []),
                        DateRangePicker::make('schedule')
                            ->label('Schedule')
                            ->withTime()
                            ->singleField()
                            ->format('Y-m-d H:i:s')
                            ->displayFormat('M j, Y H:i')
                            ->default(fn () => $record->task
                                ? ['start' => $record->task->start_at?->format('Y-m-d H:i:s'), 'end' => $record->task->end_at?->format('Y-m-d H:i:s')]
                                : null),
                    ])
                    ->action(function (Ticket $record, array $data): void {
                        static::assignOrSyncUsers($record, $data['user_ids'], $data['schedule'] ?? null);
                    })
                    ->successRedirectUrl(fn (Ticket $record) => TicketResource::getUrl('view', ['record' => $record])),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Create task and assign users, or sync users on existing task.
     *
     * @param array<int, int>                                           $userIds
     * @param array{start?: string|null, end?: string|null}|string|null $schedule
     */
    public static function assignOrSyncUsers(Ticket $record, array $userIds, array|string|null $schedule = null): void
    {
        $startAt = static::parseScheduleStart($schedule);
        $endAt = static::parseScheduleEnd($schedule);

        if (null === $record->task) {
            $board = $record->project->boards()->orderBy('position')->first();

            $task = Task::query()->create([
                'workspace_id' => $record->workspace_id,
                'project_id'   => $record->project_id,
                'user_id'      => filament()->auth()->id(),
                'board_id'     => $board?->id,
                'ticket_id'    => $record->id,
                'title'        => $record->title,
                'description'  => $record->description,
                'priority'     => 'medium',
                'start_at'     => $startAt,
                'end_at'       => $endAt,
            ]);

            $task->assignedUsers()->sync($userIds);
            $record->update(['status' => 'assigned']);
            if (! empty($userIds)) {
                app(TaskIntegrationService::class)->notifyNewAssignees($task, $userIds);
            }
        } else {
            $record->task->assignedUsers()->sync($userIds);
            if (! empty($userIds)) {
                app(TaskIntegrationService::class)->notifyNewAssignees($record->task, $userIds);
            }
            if (null !== $startAt || null !== $endAt) {
                $record->task->update(array_filter([
                    'start_at' => $startAt,
                    'end_at'   => $endAt,
                ]));
            }
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
        if (null === $schedule || ! is_array($schedule)) {
            return null;
        }

        $end = $schedule['end'] ?? null;

        return filled($end) ? Carbon::parse($end) : null;
    }
}
