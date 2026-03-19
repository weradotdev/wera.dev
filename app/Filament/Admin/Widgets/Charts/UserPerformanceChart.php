<?php

namespace App\Filament\Admin\Widgets\Charts;

use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UserPerformanceChart extends ChartWidget
{
    protected ?string $heading = 'User performance';

    protected ?string $description = 'Share of completed tasks by user (last 3 months)';

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected bool $isCollapsible = true;

    protected function getType(): string
    {
        return 'pie';
    }

    /**
     * @return array<string, mixed>
     */
    protected function getData(): array
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Workspace) {
            return [
                'datasets' => [],
                'labels'   => [],
            ];
        }

        $since = now()->subMonths(3)->startOfMonth();

        $completedByUser = Task::query()
            ->where('workspace_id', $tenant->getKey())
            ->whereHas('board', fn (Builder $q): Builder => $q->whereIn('name', ['Completed', 'Done']))
            ->where('updated_at', '>=', $since)
            ->whereHas('assignedUsers')
            ->with('assignedUsers:id,name')
            ->get()
            ->flatMap(fn (Task $task) => $task->assignedUsers->map(fn ($user) => ['user_id' => $user->id, 'name' => $user->name]))
            ->groupBy('user_id')
            ->map(fn (Collection $group): int => $group->count())
            ->sortDesc()
            ->take(10);

        $userIds = $completedByUser->keys()->all();
        $names = User::whereIn('id', $userIds)->pluck('name', 'id');

        $labels = $completedByUser->keys()->map(fn (int $id) => $names[$id] ?? "User #{$id}")->values()->all();

        $colors = [
            'rgb(59, 130, 246)',
            'rgb(34, 197, 94)',
            'rgb(249, 115, 22)',
            'rgb(168, 85, 247)',
            'rgb(236, 72, 153)',
            'rgb(234, 179, 8)',
            'rgb(20, 184, 166)',
            'rgb(239, 68, 68)',
            'rgb(99, 102, 241)',
            'rgb(132, 204, 22)',
        ];

        return [
            'datasets' => [
                [
                    'data'            => $completedByUser->values()->all(),
                    'backgroundColor' => array_slice($colors, 0, $completedByUser->count()),
                ],
            ],
            'labels' => $labels,
        ];
    }
}
