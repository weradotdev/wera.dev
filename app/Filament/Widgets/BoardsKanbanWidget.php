<?php

namespace App\Filament\Widgets;

use App\Concerns\HasEditRecordModal;
use App\Models\Board;
use App\Models\Project;
use App\Models\Task;
use App\Models\Workspace;
use Filament\Facades\Filament;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Slider;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection as SupportCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class BoardsKanbanWidget extends Widget implements HasForms
{
    use HasEditRecordModal, InteractsWithForms {
        HasEditRecordModal::form insteadof InteractsWithForms;
    }

    protected string $view = 'filament.widgets.kanban.kanban-board';

    protected static string $headerView = 'filament.widgets.kanban.kanban-header';

    protected static string $recordView = 'filament.widgets.kanban.kanban-record';

    protected static string $statusView = 'filament.widgets.kanban.kanban-status';

    protected static string $model = Task::class;

    protected static string $recordTitleAttribute = 'title';

    protected static string $recordDescriptionAttribute = 'description';

    protected static string $recordStatusAttribute = 'board_id';

    protected int|string|array $columnSpan = 'full';

    protected function getEditModalSlideOver(): bool
    {
        return true;
    }

    protected function getEditModalWidth(): string
    {
        return 'md';
    }

    protected function editRecord(int|string $recordId, array $data, array $state): void
    {
        $record = $this->getEloquentQuery()->find($recordId);

        if (!$record instanceof Model) {
            return;
        }

        $panelId = Filament::getCurrentPanel()?->getId();
        if ($panelId === 'admin') {
            $data = $this->normalizeEditModalData($data);
        } elseif ($panelId === 'developer') {
            $data = array_intersect_key($data, array_flip(['completed', 'board_id']));
        } else {
            $data = array_intersect_key($data, array_flip(['completed']));
        }

        $record->update($data);
    }

    /**
     * Normalize edit modal form data for persistence (checklist repeater → array of strings).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    /**
     * Recompute and set the progress field from checklist + completed in the edit modal form state.
     */
    protected function syncEditModalProgress(Set $set): void
    {
        $checklist      = $this->editModalFormState['checklist'] ?? [];
        $completed      = $this->editModalFormState['completed'] ?? [];
        $total          = is_array($checklist) ? count($checklist) : 0;
        $completedCount = is_array($completed) ? count($completed) : 0;
        $progress       = $total > 0 ? (int) round(($completedCount / $total) * 100) : 0;
        $set('progress', $progress);
    }

    protected function normalizeEditModalData(array $data): array
    {
        unset($data['progress']);

        if (isset($data['checklist']) && is_array($data['checklist'])) {
            $items = [];
            foreach ($data['checklist'] as $row) {
                if (is_array($row) && !empty($row['item'] ?? null)) {
                    $items[] = (string) $row['item'];
                } elseif (is_string($row) && trim($row) !== '') {
                    $items[] = trim($row);
                }
            }
            $data['checklist'] = array_values($items);
            $completed         = $data['completed'] ?? [];
            if (is_array($completed)) {
                $data['completed'] = array_values(array_intersect($completed, $data['checklist']));
            }
        }

        return $data;
    }

    protected function getEditModalFormSchema(null|int|string $recordId): array
    {
        $task             = $recordId ? $this->getEloquentQuery()->find($recordId) : null;
        $checklist        = $task?->checklist ?? [];
        $checklistOptions = is_array($checklist) && !empty($checklist)
            ? (array_is_list($checklist) ? array_combine($checklist, $checklist) : $checklist)
            : [];
        $isAdmin          = 'admin' === Filament::getCurrentPanel()?->getId();

        $titleField = $isAdmin
            ? TextInput::make(static::$recordTitleAttribute)
            : TextEntry::make('title_display')
                ->extraAttributes([
                    'class' => 'text-lg font-bold',
                ])
                ->hiddenLabel()
                ->state($task?->title ?? '');

        $descriptionField = $isAdmin
            ? Textarea::make(static::$recordDescriptionAttribute)
            : TextEntry::make('description_display')->hiddenLabel()
                ->state($task?->description ?? '');

        $schema = [
            $titleField,
            $descriptionField,
        ];

        if ($isAdmin) {
            $schema[] = Repeater::make('checklist')
                ->label('Checklist items')
                ->simple(
                    TextInput::make('item')
                        ->label('Item')
                        ->required()
                )
                ->default($checklist)
                ->addActionLabel('Add item')
                ->columnSpanFull()
                ->collapsible()
                ->live()
                ->afterStateUpdated(function (Set $set): void {
                    $this->syncEditModalProgress($set);
                });
        }

        $schema[] = CheckboxList::make('completed')
            ->label('Completed items')
            ->options($checklistOptions)
            ->visible(fn(): bool => !empty($checklistOptions))
            ->live()
            ->afterStateUpdated(function (array $state, Set $set): void {
                $this->syncEditModalProgress($set);
            });

        $schema[] = Slider::make('progress')
            ->label('Progress')
            ->range(minValue: 0, maxValue: 100)
            ->dehydrated(false)
            ->disabled()
            ->fillTrack()
            ->live()
            ->hint(fn(): string => $this->editModalFormState['progress'] . '%');

        $schema[] = Placeholder::make('assigned_users')
            ->label('Assigned')
            ->content(fn(): HtmlString => $this->getKanbanAssignedUsersPlaceholderHtml());

        $panelId     = Filament::getCurrentPanel()?->getId();
        $canMoveTask = $panelId === 'admin' || $panelId === 'developer';
        if ($canMoveTask) {
            $project = $this->resolveProject();
            if ($project) {
                $boardsQuery  = $project->boards()->orderBy('position');
                $boardOptions = $panelId === 'admin'
                    ? $boardsQuery->pluck('name', 'id')->all()
                    : $boardsQuery->get()->slice(0, -1)->pluck('name', 'id')->all();

                $schema[] = Select::make('board_id')
                    ->label('Move task to')
                    ->options($boardOptions)
                    ->required()
                    ->searchable();
            }
        }

        return $schema;
    }

    protected function getEditModalRecordData(int|string $recordId, array $data): array
    {
        $record = $this->getEloquentQuery()->find($recordId);

        if (!$record instanceof Model) {
            return $data;
        }

        $recordData             = $record->toArray();
        $recordData['progress'] = $record->progress;
        $checklist              = $record->checklist ?? [];

        if (is_array($checklist) && !empty($checklist)) {
            $recordData['checklist'] = array_values(array_map(
                fn(string $item): array => ['item' => $item],
                array_filter($checklist, fn($item): bool => trim((string) $item) !== '')
            ));
        } else {
            $recordData['checklist'] = [];
        }

        return $recordData;
    }

    protected function getKanbanAssignedUsersPlaceholderHtml(): HtmlString
    {
        $task = $this->editModalRecordId
            ? $this->getEloquentQuery()->with('assignedUsers')->find($this->editModalRecordId)
            : null;

        if (!$task || !$task->assignedUsers || $task->assignedUsers->isEmpty()) {
            return new HtmlString('<p class="text-sm text-gray-500 dark:text-gray-400">No one assigned</p>');
        }

        $html = '<div class="flex flex-wrap gap-3">';

        foreach ($task->assignedUsers as $user) {
            $avatarUrl  = method_exists($user, 'getFilamentAvatarUrl') ? $user->getFilamentAvatarUrl() : $user->avatar;
            $name       = e($user->name);
            $html      .= '<div class="flex items-center gap-2">';
            $html      .= '<img src="' . e($avatarUrl) . '" alt="' . $name . '" class="fi-avatar fi-circular fi-size-sm rounded-full ring-2 ring-gray-200 dark:ring-gray-600" title="' . $name . '" />';
            $html      .= '<span class="text-sm font-medium text-gray-700 dark:text-gray-200">' . $name . '</span>';
            $html      .= '</div>';
        }

        $html .= '</div>';

        return new HtmlString($html);
    }

    public ?Project $record = null;

    public string $newBoardName = '';

    public bool $showAddListForm = false;

    public ?int $addingCardToBoardId = null;

    public string $newCardTitle = '';

    protected $listeners = ['cancel-add-task' => 'cancelAddingCard', 'task-added' => 'handleTaskAdded'];

    public int $kanbanRefreshKey = 0;

    public function editModalFormSubmitted(): void
    {
        $this->editRecord($this->editModalRecordId, $this->form->getState(), $this->editModalFormState);

        $this->editModalRecordId = null;
        $this->form->fill();

        $this->dispatch('close-modal', id: 'kanban--edit-record-modal');

        $this->kanbanRefreshKey++;
        $this->dispatch('kanban-boards-updated');
    }

    public function startAddingCard(int $boardId): void
    {
        $this->addingCardToBoardId = $boardId;
        $this->newCardTitle        = '';
        $this->resetValidation('newCardTitle');
        $this->dispatch('open-modal', id: 'kanban--add-task-modal');
    }

    public function cancelAddingCard(): void
    {
        $this->addingCardToBoardId = null;
        $this->newCardTitle        = '';
        $this->resetValidation('newCardTitle');
        $this->dispatch('close-modal', id: 'kanban--add-task-modal');
    }

    public function handleTaskAdded(): void
    {
        $this->addingCardToBoardId = null;
        $this->dispatch('close-modal', id: 'kanban--add-task-modal');
    }

    public function addCard(): void
    {
        $project = $this->resolveProject();

        if (!$project || null === $this->addingCardToBoardId) {
            return;
        }

        $this->validate([
            'newCardTitle' => ['required', 'string', 'max:255'],
        ]);

        $boardExists = $project->boards()->whereKey($this->addingCardToBoardId)->exists();

        if (!$boardExists) {
            $this->cancelAddingCard();

            return;
        }

        $nextPosition = ((int) Task::query()
            ->where('board_id', $this->addingCardToBoardId)
            ->max('position')) + 1;

        Task::query()->create([
            'workspace_id' => $project->workspace_id,
            'project_id'   => $project->id,
            'user_id'      => filament()->auth()->id(),
            'board_id'     => $this->addingCardToBoardId,
            'title'        => trim($this->newCardTitle),
            'description'  => null,
            'priority'     => 'medium',
            'position'     => $nextPosition,
        ]);

        $this->cancelAddingCard();
    }

    /**
     * @param array<int, int> $orderedBoardIds
     */
    public function moveBoardLeft(int $boardId, array $orderedBoardIds): void
    {
        $currentIndex = array_search($boardId, $orderedBoardIds, true);

        if (false === $currentIndex || $currentIndex < 1) {
            return;
        }

        $newOrder                                                = $orderedBoardIds;
        [$newOrder[$currentIndex - 1], $newOrder[$currentIndex]] = [$newOrder[$currentIndex], $newOrder[$currentIndex - 1]];

        $this->moveBoard($boardId, $currentIndex - 1, $newOrder);
    }

    /**
     * @param array<int, int> $orderedBoardIds
     */
    public function moveBoardRight(int $boardId, array $orderedBoardIds): void
    {
        $currentIndex = array_search($boardId, $orderedBoardIds, true);

        if (false === $currentIndex || $currentIndex >= count($orderedBoardIds) - 1) {
            return;
        }

        $newOrder                                                = $orderedBoardIds;
        [$newOrder[$currentIndex], $newOrder[$currentIndex + 1]] = [$newOrder[$currentIndex + 1], $newOrder[$currentIndex]];

        $this->moveBoard($boardId, $currentIndex + 1, $newOrder);
    }

    public function removeBoard(int $boardId): void
    {
        $project = $this->resolveProject();

        if (!$project) {
            return;
        }

        $board = $project->boards()->whereKey($boardId)->first();

        if (!$board instanceof Board) {
            return;
        }

        $otherBoard = $project->boards()
            ->where('id', '!=', $boardId)
            ->orderBy('position')
            ->first();

        $taskCount = Task::query()->where('board_id', $boardId)->count();

        if ($taskCount > 0 && !$otherBoard) {
            return;
        }

        if ($otherBoard) {
            Task::query()
                ->where('board_id', $boardId)
                ->update(['board_id' => $otherBoard->id]);
        }

        $board->delete();
    }

    /**
     * @param array<int, int|string> $orderedBoardIds Board ids in new order
     */
    public function moveBoard(int $boardId, int $targetIndex, array $orderedBoardIds): void
    {
        $project = $this->resolveProject();

        if (!$project) {
            return;
        }

        $boardExists = $project->boards()->whereKey($boardId)->exists();

        if (!$boardExists) {
            return;
        }

        $this->syncBoardPositions($project, $orderedBoardIds);
    }

    public function createBoard(): void
    {
        $project = $this->resolveProject();

        if (!$project) {
            return;
        }

        $validated = $this->validate([
            'newBoardName' => ['required', 'string', 'max:255'],
        ]);

        $boardName = trim($validated['newBoardName']);

        if ('' === $boardName) {
            $this->addError('newBoardName', 'Board name is invalid.');

            return;
        }

        if ($project->boards()->where('name', $boardName)->exists()) {
            $this->addError('newBoardName', 'That board already exists in this project.');

            return;
        }

        $nextPosition = ((int) ($project->boards()->max('position') ?? -1)) + 1;

        $project->boards()->create([
            'name'     => $boardName,
            'position' => $nextPosition,
        ]);

        $this->resetValidation('newBoardName');
        $this->newBoardName    = '';
        $this->showAddListForm = false;
        $this->dispatch('kanban-boards-updated');
    }

    public function toggleAddListForm(): void
    {
        $this->showAddListForm = !$this->showAddListForm;
        if (!$this->showAddListForm) {
            $this->newBoardName = '';
            $this->resetValidation('newBoardName');
        }
    }

    public function sortTask(int|string $taskId, int $targetIndex, int|string $targetBoardId): void
    {
        $project = $this->resolveProject();

        if (!$project) {
            return;
        }

        $normalizedTaskId      = (int) $taskId;
        $normalizedBoardId     = (int) $targetBoardId;
        $normalizedTargetIndex = max(0, $targetIndex);

        $task = Task::query()
            ->whereKey($normalizedTaskId)
            ->where('project_id', $project->id)
            ->first();

        if (!$task) {
            return;
        }

        $targetBoardExists = $project->boards()->whereKey($normalizedBoardId)->exists();

        if (!$targetBoardExists) {
            return;
        }

        $sourceBoardId = (int) $task->board_id;

        DB::transaction(function () use ($project, $task, $normalizedTaskId, $normalizedBoardId, $normalizedTargetIndex, $sourceBoardId): void {
            $destinationTaskIds = Task::query()
                ->where('project_id', $project->id)
                ->where('board_id', $normalizedBoardId)
                ->whereKeyNot($normalizedTaskId)
                ->orderBy('position')
                ->orderBy('id')
                ->pluck('id')
                ->map(fn(int|string $id): int => (int) $id)
                ->all();

            $clampedTargetIndex = min($normalizedTargetIndex, count($destinationTaskIds));

            array_splice($destinationTaskIds, $clampedTargetIndex, 0, [$normalizedTaskId]);

            $this->resequenceBoardTasks($normalizedBoardId, $destinationTaskIds);

            if ($sourceBoardId !== $normalizedBoardId) {
                $sourceTaskIds = Task::query()
                    ->where('project_id', $project->id)
                    ->where('board_id', $sourceBoardId)
                    ->whereKeyNot($normalizedTaskId)
                    ->orderBy('position')
                    ->orderBy('id')
                    ->pluck('id')
                    ->map(fn(int|string $id): int => (int) $id)
                    ->all();

                $this->resequenceBoardTasks($sourceBoardId, $sourceTaskIds);
            }
        });

        $this->kanbanRefreshKey++;
        $this->dispatch('kanban-boards-updated');
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $project  = $this->resolveProject();
        $records  = $this->records();
        $statuses = $this->statuses()
            ->values()
            ->map(function (array $status, int $index) use ($records): array {
                $status['records'] = $this->filterRecordsByStatus($records, $status);
                $status['index']   = $index;

                return $status;
            });

        $orderedBoardIds = $statuses->pluck('id')->values()->all();

        return match (true) {
            !$project => [
                'project'         => null,
                'statuses'        => collect(),
                'orderedBoardIds' => [],
            ],
            default => [
                'project'         => $project,
                'statuses'        => $statuses,
                'orderedBoardIds' => $orderedBoardIds,
            ],
        };
    }

    protected function statuses(): SupportCollection
    {
        $project = $this->resolveProject();

        if (!$project) {
            return collect();
        }

        return $project->boards()
            ->orderBy('position')
            ->get()
            ->map(fn(Board $board): array => [
                'id'    => $board->id,
                'title' => $board->name,
            ]);
    }

    protected function records(): SupportCollection
    {
        return $this->getEloquentQuery()
            ->when(method_exists(static::$model, 'scopeOrdered'), fn($query) => $query->ordered())
            ->get();
    }

    /**
     * @param  array{id: int, title: string} $status
     * @return array<int, Task>
     */
    protected function filterRecordsByStatus(SupportCollection $records, array $status): array
    {
        return $records->where(static::$recordStatusAttribute, $status['id'])->all();
    }

    protected function getEloquentQuery(): Builder
    {
        $project = $this->resolveProject();

        return Task::query()
            ->when($project, fn(Builder $query): Builder => $query->where('project_id', $project->id))
            ->with('assignedUsers')
            ->orderBy('position')
            ->orderBy('id');
    }

    private function resolveProject(): ?Project
    {
        if ($this->record) {
            return $this->record->loadMissing('boards');
        }

        $tenant = Filament::getTenant();

        $projectQuery = Project::query()->with('boards');

        return match (true) {
            $tenant instanceof Project => $projectQuery->whereKey($tenant)->first(),
            $tenant instanceof Workspace => $projectQuery->whereBelongsTo($tenant)->latest('id')->first(),
            default => null,
        };
    }

    /**
     * @param array<int, int> $taskIds
     */
    private function resequenceBoardTasks(int $boardId, array $taskIds): void
    {
        foreach ($taskIds as $position => $orderedTaskId) {
            Task::query()
                ->whereKey($orderedTaskId)
                ->update([
                    'board_id' => $boardId,
                    'position' => $position,
                ]);
        }
    }

    /**
     * @param array<int, int|string> $orderedBoardIds
     */
    private function syncBoardPositions(Project $project, array $orderedBoardIds): void
    {
        $existingBoardIds = $project->boards()
            ->pluck('id')
            ->map(fn(int|string $id): int => (int) $id)
            ->all();

        $normalizedOrderedBoardIds = collect($orderedBoardIds)
            ->map(fn(int|string $id): int => (int) $id)
            ->filter(fn(int $id): bool => in_array($id, $existingBoardIds, true))
            ->values();

        $missingBoardIds = collect($existingBoardIds)
            ->reject(fn(int $id): bool => $normalizedOrderedBoardIds->contains($id));

        $finalOrdering = $normalizedOrderedBoardIds->concat($missingBoardIds)->values();

        foreach ($finalOrdering as $position => $orderedBoardId) {
            $project->boards()
                ->whereKey($orderedBoardId)
                ->update(['position' => $position]);
        }
    }

}
