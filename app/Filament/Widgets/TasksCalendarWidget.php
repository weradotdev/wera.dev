<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use Filament\Facades\Filament;
use Guava\Calendar\Enums\CalendarViewType;
use Guava\Calendar\Filament\Actions\ViewAction;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\EventDropInfo;
use Guava\Calendar\ValueObjects\EventResizeInfo;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class TasksCalendarWidget extends CalendarWidget
{
    protected static ?int $sort = 999;

    protected int|string|array $columnSpan = 'full';

    protected CalendarViewType $calendarView = CalendarViewType::DayGridMonth;

    protected bool $eventDragEnabled = true;

    protected bool $eventResizeEnabled = true;

    protected ?string $defaultEventClickAction = 'view';

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public function viewAction(): ViewAction
    {
        return parent::viewAction()
            ->slideOver()
            ->modalHeading('Task details');
    }

    protected function getEvents(FetchInfo $info): Collection|array|Builder
    {
        $tenant = Filament::getTenant();

        $query = Task::query()
            ->whereNotNull('start_at')
            ->when($tenant instanceof Project, fn (Builder $builder): Builder => $builder->where('project_id', $tenant->getKey()))
            ->when($tenant instanceof Workspace, fn (Builder $builder): Builder => $builder->where('workspace_id', $tenant->getKey()))
            ->with(['assignedUsers', 'board'])
            ->where(function (Builder $builder) use ($info): void {
                $builder
                    ->whereBetween('start_at', [$info->start, $info->end])
                    ->orWhereBetween('end_at', [$info->start, $info->end])
                    ->orWhere(function (Builder $query) use ($info): void {
                        $query
                            ->where('start_at', '<=', $info->start)
                            ->where(function (Builder $inner) use ($info): void {
                                $inner
                                    ->where('end_at', '>=', $info->end)
                                    ->orWhereNull('end_at');
                            });
                    });
            });

        return $query
            ->get()
            ->map(function (Task $task): CalendarEvent {
                return CalendarEvent::make()
                    ->model(Task::class)
                    ->key($task->getKey())
                    ->title($task->title)
                    ->start($task->start_at)
                    ->end($task->end_at ?? $task->start_at?->copy()->addHour())
                    ->extendedProps([
                        'priority' => $task->priority,
                        'board'    => $task->board?->name,
                        'assignee' => $task->assignedUsers->pluck('name')->join(', ') ?: null,
                    ]);
            });
    }

    protected function onEventDrop(EventDropInfo $info, Model $event): bool
    {
        if (! $event instanceof Task) {
            return false;
        }

        $tenant = Filament::getTenant();

        if ($tenant instanceof Project && $event->project_id !== $tenant->getKey()) {
            return false;
        }

        if ($tenant instanceof Workspace && $event->workspace_id !== $tenant->getKey()) {
            return false;
        }

        $event->update([
            'start_at' => $info->event->getStart(),
            'end_at'   => $info->event->getEnd(),
        ]);

        $this->refreshRecords();

        return true;
    }

    public function onEventResize(EventResizeInfo $info, Model $event): bool
    {
        if (! $event instanceof Task) {
            return false;
        }

        $tenant = Filament::getTenant();

        if ($tenant instanceof Project && $event->project_id !== $tenant->getKey()) {
            return false;
        }

        if ($tenant instanceof Workspace && $event->workspace_id !== $tenant->getKey()) {
            return false;
        }

        $event->update([
            'start_at' => $info->event->getStart(),
            'end_at'   => $info->event->getEnd(),
        ]);

        $this->refreshRecords();

        return true;
    }
}
