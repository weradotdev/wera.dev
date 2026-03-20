<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskUser;
use App\Models\User;
use App\Models\Workspace;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class AddTaskKanbanForm extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public ?int $boardId = null;

    /**
     * Form state for add-task schema (statePath 'data').
     *
     * @var array<string, mixed>
     */
    public array $data = [];

    public function mount(?int $boardId = null): void
    {
        $this->boardId = $boardId;

        $this->form->fill($this->getDefaultFormState());
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultFormState(): array
    {
        $state = [
            'title'       => '',
            'description' => null,
            'priority'    => 'medium',
            'checklist'   => [],
            'screenshots' => [],
        ];

        if ($this->isWorkspacePanel()) {
            $state['assigned_user_ids'] = [];
        }

        return $state;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components($this->getAddTaskFormSchema())
            ->statePath('data')
            ->model($this->getFormModel());
    }

    /**
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    protected function getAddTaskFormSchema(): array
    {
        $schema = [
            TextInput::make('title')
                ->required()
                ->maxLength(255)
                ->columnSpanFull(),
            Textarea::make('description')
                ->columnSpanFull(),
            Radio::make('priority')
                ->options([
                    'low'    => 'Low',
                    'medium' => 'Medium',
                    'high'   => 'High',
                ])
                ->default('medium')
                ->inline()
                ->required(),
            Repeater::make('checklist')
                ->label('Checklist items')
                ->default([])
                ->simple(
                    TextInput::make('item')
                        ->label('Item')
                )
                ->addActionLabel('Add item')
                ->columnSpanFull()
                ->collapsible(),
            FileUpload::make('screenshots')
                ->label('Screenshots / Attachments')
                ->multiple()
                ->reorderable()
                ->columnSpanFull(),
        ];

        if ($this->isWorkspacePanel()) {
            $workspace = Filament::getTenant();
            $schema[]  = Select::make('assigned_user_ids')
                ->label('Assigned users')
                ->options(
                    $workspace instanceof Workspace
                    ? User::query()
                        ->whereHas('workspaces', fn(Builder $q): Builder => $q->where('workspaces.id', $workspace->getKey()))
                        ->orderBy('name')
                        ->pluck('name', 'id')
                    : []
                )
                ->multiple()
                ->searchable()
                ->columnSpanFull();
        }

        return $schema;
    }

    protected function isWorkspacePanel(): bool
    {
        return Filament::getCurrentPanel()?->getId() === 'admin';
    }

    /**
     * @param  mixed  $checklist  Repeater state (array of ['item' => string] or list of strings)
     * @return array<int, string>
     */
    protected function normalizeChecklistToItems(mixed $checklist): array
    {
        if (!is_array($checklist)) {
            return [];
        }

        $items = [];
        foreach ($checklist as $row) {
            if (is_array($row) && !empty($row['item'] ?? null)) {
                $items[] = (string) $row['item'];
            } elseif (is_string($row) && trim($row) !== '') {
                $items[] = trim($row);
            }
        }

        return array_values($items);
    }

    protected function getFormModel(): Model
    {
        $task           = new Task;
        $task->board_id = $this->boardId;
        $task->priority = 'medium';

        return $task;
    }

    public function submit(): void
    {
        $project = $this->resolveProject();

        if (!$project || !$this->boardId) {
            return;
        }

        $data = $this->form->getState();

        $boardExists = $project->boards()->whereKey($this->boardId)->exists();
        if (!$boardExists) {
            return;
        }

        $nextPosition = ((int) Task::query()
            ->where('board_id', $this->boardId)
            ->max('position')) + 1;

        $checklist      = $data['checklist'] ?? [];
        $checklistItems = $this->normalizeChecklistToItems($checklist);

        $task = Task::query()->create([
            'workspace_id' => $project->workspace_id,
            'project_id'   => $project->id,
            'user_id'      => filament()->auth()->id(),
            'board_id'     => $this->boardId,
            'title'        => trim($data['title']),
            'description'  => filled($data['description'] ?? null) ? $data['description'] : null,
            'priority'     => $data['priority'] ?? 'medium',
            'checklist'    => $checklistItems,
            'position'     => $nextPosition,
        ]);

        $screenshots = $data['screenshots'] ?? [];
        if (is_array($screenshots)) {
            foreach ($screenshots as $file) {
                if ($file instanceof TemporaryUploadedFile && $file->exists()) {
                    $task->addMedia($file)->toMediaCollection('screenshots');
                }
            }
        }

        if ($this->isWorkspacePanel()) {
            $assignedUserIds = $data['assigned_user_ids'] ?? [];
            if (is_array($assignedUserIds)) {
                foreach ($assignedUserIds as $userId) {
                    TaskUser::query()->create([
                        'task_id' => $task->id,
                        'user_id' => $userId,
                        'role'    => 'developer',
                    ]);
                }
            }
        } else {
            TaskUser::query()->create([
                'task_id' => $task->id,
                'user_id' => filament()->auth()->id(),
                'role'    => 'developer',
            ]);
        }

        $this->form->fill($this->getDefaultFormState());
        $this->dispatch('task-added');
        $this->dispatch('close-modal', id: 'kanban--add-task-modal');
    }

    public function cancel(): void
    {
        $this->dispatch('close-modal', id: 'kanban--add-task-modal');
        $this->dispatch('cancel-add-task');
    }

    private function resolveProject(): ?Project
    {
        $tenant = Filament::getTenant();

        $projectQuery = Project::query()->with('boards');

        return match (true) {
            $tenant instanceof Project => $projectQuery->whereKey($tenant)->first(),
            $tenant instanceof Workspace => $projectQuery->whereBelongsTo($tenant)->latest('id')->first(),
            default => null,
        };
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('filament.widgets.add-task-kanban-form');
    }
}
