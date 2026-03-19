<?php

namespace App\Filament\Admin\Widgets\Charts;

use App\Models\Task;
use App\Models\User;
use App\Models\Workspace;
use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class TopUsersChart extends ChartWidget
{
    protected ?string $heading = 'User performance';

    protected ?string $description = 'Top users by completed tasks (last 3 months)';

    protected ?string $maxHeight = '300px';

    protected ?string $pollingInterval = null;

    protected bool $isCollapsible = true;

    protected function getType(): string
    {
        return 'bar';
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
        $names = User::query()->whereIn('id', $userIds)->pluck('name', 'id');

        $labels = $completedByUser->keys()->map(fn (int $id) => $names[$id] ?? "User #{$id}")->values()->all();

        return [
            'datasets' => [
                [
                    'label' => 'Completed tasks',
                    'data'  => $completedByUser->values()->all(),
                ],
            ],
            'labels' => $labels,
        ];
    }
}
