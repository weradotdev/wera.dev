<?php

namespace App\Http\Controllers;

use App\Models\Board;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Dedoc\Scramble\Attributes\Group;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

#[Group('Stats')]
class StatsController extends Controller
{
    /**
     * Get stats based on user role.
     */
    public function make(Request $request, string $type = 'developer'): JsonResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (! $user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $data = match (strtolower($type)) {
            'owner', 'admin', 'project_manager' => $this->owner($user),
            default => $this->developer($user),
        };

        return response()->json($data, 200);
    }

    /**
     * Get developer stats (personal task-focused metrics).
     * Tasks are classified by their board position within each project:
     *   last board = completed, first board = todo, anything in between = in progress.
     *
     * @param  User                 $user
     * @return array<string, mixed>
     */
    protected function developer($user): array
    {
        $tasks = Task::query()
            ->whereHas('assignedUsers', fn ($q) => $q->where('users.id', $user->id))
            ->get(['id', 'project_id', 'board_id', 'end_at']);

        $counts = $this->classifyByBoards($tasks);

        $activeProjects = Project::query()
            ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
            ->count();

        return [
            'total_tasks'       => $counts['total'],
            'completed_tasks'   => $counts['completed'],
            'in_progress_tasks' => $counts['in_progress'],
            'overdue_tasks'     => $counts['overdue'],
            'active_projects'   => $activeProjects,
            'completion_rate'   => $counts['total'] > 0
                ? (int) round(($counts['completed'] / $counts['total']) * 100)
                : 0,
        ];
    }

    /**
     * Get owner/admin stats (workspace-wide metrics).
     *
     * @param  User                 $user
     * @return array<string, mixed>
     */
    protected function owner($user): array
    {
        $userWorkspaceIds = $user->workspaces()
            ->wherePivotIn('role', ['owner', 'admin'])
            ->pluck('workspaces.id')
            ->toArray();

        $tasks = Task::query()
            ->whereIn('workspace_id', $userWorkspaceIds)
            ->get(['id', 'project_id', 'board_id', 'end_at']);

        $counts = $this->classifyByBoards($tasks);

        $teamMembers = $user->workspaces()
            ->wherePivotIn('role', ['owner', 'admin'])
            ->with('users')
            ->get()
            ->pluck('users')
            ->flatten()
            ->unique('id')
            ->count();

        $activeProjects = Project::query()
            ->whereIn('workspace_id', $userWorkspaceIds)
            ->count();

        return [
            'total_tasks'       => $counts['total'],
            'completed_tasks'   => $counts['completed'],
            'in_progress_tasks' => $counts['in_progress'],
            'overdue_tasks'     => $counts['overdue'],
            'active_projects'   => $activeProjects,
            'team_members'      => $teamMembers,
            'completion_rate'   => $counts['total'] > 0
                ? (int) round(($counts['completed'] / $counts['total']) * 100)
                : 0,
        ];
    }

    /**
     * Classify a flat task collection by board position within each project.
     * Loads all relevant boards in a single query (no N+1).
     *
     * @param  Collection<int, Task>                                             $tasks
     * @return array{total: int, completed: int, in_progress: int, overdue: int}
     */
    protected function classifyByBoards(Collection $tasks): array
    {
        if ($tasks->isEmpty()) {
            return ['total' => 0, 'completed' => 0, 'in_progress' => 0, 'overdue' => 0];
        }

        $projectIds = $tasks->pluck('project_id')->unique()->filter()->values()->toArray();

        // Single query: all boards for all relevant projects, grouped by project_id
        $boardsByProject = Board::query()
            ->whereIn('project_id', $projectIds)
            ->orderBy('project_id')
            ->orderBy('position')
            ->get(['id', 'project_id'])
            ->groupBy('project_id');

        $total = $tasks->count();
        $completed = 0;
        $inProgress = 0;
        $overdue = 0;

        foreach ($tasks as $task) {
            $boards = $boardsByProject->get($task->project_id);

            if (! $boards || $boards->isEmpty()) {
                continue;
            }

            $lastBoardId = $boards->last()->id;
            $firstBoardId = $boards->first()->id;
            $isDone = $task->board_id === $lastBoardId;

            if ($isDone) {
                $completed++;
            } elseif ($task->board_id !== $firstBoardId) {
                $inProgress++;
            }

            if (! $isDone && $task->end_at && $task->end_at->isPast()) {
                $overdue++;
            }
        }

        return [
            'total'       => $total,
            'completed'   => $completed,
            'in_progress' => $inProgress,
            'overdue'     => $overdue,
        ];
    }

    /**
     * Resolve which method to call based on user type.
     */
    protected function resolveMethod(string $userType): string
    {
        return match ($userType) {
            'owner', 'admin', 'project_manager' => 'owner',
            default => 'developer',
        };
    }
}
