<?php

namespace App\Filament\Admin\Resources\Tickets\Pages;

use App\Filament\Admin\Resources\Tickets\Tables\TicketsTable;
use App\Filament\Admin\Resources\Tickets\TicketResource;
use App\Models\Ticket;
use App\Models\User;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class ViewTicket extends ViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Ticket $record */
        $record = $this->record;

        return [
            Action::make('assign')
                ->label('Assign to users')
                ->icon('heroicon-o-user-plus')
                ->color('primary')
                ->visible(fn () => in_array($record->status, ['open', 'assigned'], true))
                ->schema(fn (): array => [
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
                ->action(function (array $data): void {
                    TicketsTable::assignOrSyncUsers($this->record, $data['user_ids'], $data['schedule'] ?? null);
                })
                ->successRedirectUrl(fn () => TicketResource::getUrl('view', ['record' => $record])),
            EditAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->title;
    }
}
