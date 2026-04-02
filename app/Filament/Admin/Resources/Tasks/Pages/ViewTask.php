<?php

namespace App\Filament\Admin\Resources\Tasks\Pages;

use App\Filament\Admin\Resources\Tasks\Schemas\TaskForm;
use App\Filament\Admin\Resources\Tasks\Tables\TasksTable;
use App\Filament\Admin\Resources\Tasks\TaskResource;
use App\Models\Task;
use App\Models\User;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ViewTask extends ViewRecord
{
    protected static string $resource = TaskResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Task $record */
        $record = $this->record;

        return [
            Action::make('assign')
                ->label('Assignment')
                ->icon('heroicon-o-user-plus')
                ->color('primary')
                ->modalWidth('lg')
                ->schema(fn (): array => [
                    Repeater::make('assignments')
                        ->label('Assigned users')
                        ->schema([
                            Select::make('user_id')
                                ->label('User')
                                ->options(
                                    User::query()
                                        ->whereHas('workspaces', fn (Builder $q) => $q->where('workspaces.id', $record->workspace_id))
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
                        ->addActionLabel('Assign another user')
                        ->columns(2)
                        ->default(fn () => $record->assignedUsers->map(fn ($user) => [
                            'user_id' => $user->id,
                            'role'    => $user->pivot->role ?? 'developer',
                        ])->toArray()),
                    DateRangePicker::make('schedule')
                        ->label('Schedule')
                        ->withTime()
                        ->singleField()
                        ->format('Y-m-d H:i:s')
                        ->displayFormat('M j, Y H:i')
                        ->default(fn () => $record->start_at && $record->end_at
                            ? ['start' => $record->start_at->format('Y-m-d H:i:s'), 'end' => $record->end_at->format('Y-m-d H:i:s')]
                            : null),
                ])
                ->action(function (array $data): void {
                    TasksTable::assignUsers($this->record, $data['assignments'] ?? [], $data['schedule'] ?? null);
                })
                ->successRedirectUrl(fn () => TaskResource::getUrl('view', ['record' => $record])),
            EditAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->title;
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->record->workspace->name;
    }
}
