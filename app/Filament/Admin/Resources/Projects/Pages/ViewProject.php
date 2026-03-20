<?php

namespace App\Filament\Admin\Resources\Projects\Pages;

use App\Events\GenerateDevelopmentPlanRequested;
use App\Filament\Admin\Resources\Projects\ProjectResource;
use App\Filament\Admin\Resources\Projects\Resources\Meetings\MeetingResource;
use App\Filament\Widgets\BoardsKanbanWidget;
use App\Models\Meeting;
use App\Models\Plan;
use App\Models\PlanRevision;
use App\Models\Project;
use App\Models\User;
use App\Models\Workspace;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        /** @var Project $project */
        $project = $this->record;

        return [
            ActionGroup::make([
                Action::make('generatePlan')
                    ->label('Generate Plan')
                    ->icon('hugeicons-bot')
                    ->color('primary')
                    ->slideOver()
                    ->modalIcon('hugeicons-bot')
                    ->modalWidth('md')
                    ->modalSubheading('Generate a plan for the project')
                    ->modalSubmitActionLabel('Generate Plan')
                    ->form([
                        Textarea::make('description')
                            ->label('Description')
                            ->placeholder('Project context and scope for the implementation plan')
                            ->helperText('Provide a description of the project and its scope for the implementation plan. Add any relevant information that will help the AI generate a comprehensive plan.')
                            ->hint('Min. 500 chars')
                            ->default(fn (): string => (string) $project->description)
                            ->required()
                            ->rows(6)
                            ->minLength(500)
                            ->columnSpanFull(),
                        Select::make('user_ids')
                            ->label('Users')
                            ->helperText('Optional: select users to include in the plan context')
                            ->options(
                                User::query()
                                    ->whereHas('workspaces', fn (Builder $q): Builder => $q->where('workspaces.id', $project->workspace_id))
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                            )
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ])
                    ->action(function (array $data): void {
                        /** @var Project $project */
                        $project = $this->record;

                        $plan = Plan::query()->create([
                            'workspace_id'  => $project->workspace_id,
                            'user_id'       => filament()->auth()->id(),
                            'planable_type' => Project::class,
                            'planable_id'   => $project->getKey(),
                            'name'          => "{$project->name} Development Plan",
                            'description'   => $data['description'],
                        ]);

                        PlanRevision::query()->create([
                            'plan_id'     => $plan->id,
                            'name'        => $project->name.' — Initial',
                            'description' => $data['description'],
                        ]);

                        GenerateDevelopmentPlanRequested::dispatch($project, $plan);
                    })
                    ->successNotificationTitle('Plan created. AI generation has been queued and will add a revision when ready.'),

                Action::make('exportExecutiveReport')
                    ->label('Export Executive PDF')
                    ->icon('heroicon-o-document-chart-bar')
                    ->color('gray')
                    ->action(function () {
                        /** @var Project $project */
                        $project = $this->record->load([
                            'workspace',
                            'comments.author',
                            'tasks' => fn ($query) => $query
                                ->with(['board', 'assignedUsers', 'comments.author'])
                                ->orderBy('end_at')
                                ->orderBy('id'),
                        ]);

                        $reportData = $this->buildExecutiveReportData($project);

                        $pdf = Pdf::loadView('reports.project-executive', [
                            'project'       => $project,
                            'reportData'    => $reportData,
                            'generatedAt'   => now(),
                            'workspaceLogo' => $this->resolveLogoDataUri($project->workspace?->image),
                            'projectLogo'   => $this->resolveLogoDataUri($project->image),
                        ])->setPaper('a4');

                        $filename = sprintf('project-executive-report-%s-%s.pdf', $project->id, now()->format('Ymd-His'));

                        return response()->streamDownload(
                            fn () => print ($pdf->output()),
                            $filename,
                        );
                    }),
                Action::make('exportStatusReport')
                    ->label('Export Status PDF')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('gray')
                    ->action(function () {
                        /** @var Project $project */
                        $project = $this->record->load([
                            'workspace',
                            'comments.author',
                            'tasks' => fn ($query) => $query
                                ->with(['board', 'assignedUsers', 'comments.author'])
                                ->orderBy('start_at')
                                ->orderBy('id'),
                        ]);

                        $reportData = $this->buildReportData($project);

                        $pdf = Pdf::loadView('reports.project-status', [
                            'project'       => $project,
                            'reportData'    => $reportData,
                            'generatedAt'   => now(),
                            'workspaceLogo' => $this->resolveLogoDataUri($project->workspace?->image),
                            'projectLogo'   => $this->resolveLogoDataUri($project->image),
                        ])->setPaper('a4');

                        $filename = sprintf('project-status-report-%s-%s.pdf', $project->id, now()->format('Ymd-His'));

                        return response()->streamDownload(
                            fn () => print ($pdf->output()),
                            $filename,
                        );
                    }),
                Action::make('exportPortfolioReport')
                    ->label('Export Portfolio PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('gray')
                    ->action(function () {
                        /** @var Project $project */
                        $project = $this->record->load('workspace');

                        /** @var Workspace $workspace */
                        $workspace = Workspace::query()
                            ->whereKey($project->workspace_id)
                            ->with([
                                'projects' => fn ($query) => $query
                                    ->with([
                                        'comments.author',
                                        'tasks' => fn ($taskQuery) => $taskQuery
                                            ->with(['board', 'assignedUsers', 'comments.author'])
                                            ->orderBy('start_at')
                                            ->orderBy('id'),
                                    ])
                                    ->orderBy('name'),
                            ])
                            ->firstOrFail();

                        $portfolioData = $this->buildPortfolioReportData($workspace);

                        $pdf = Pdf::loadView('reports.workspace-portfolio', [
                            'workspace'     => $workspace,
                            'portfolioData' => $portfolioData,
                            'generatedAt'   => now(),
                            'workspaceLogo' => $this->resolveLogoDataUri($workspace->image),
                            'projectLogo'   => $this->resolveLogoDataUri($project->image),
                        ])->setPaper('a4');

                        $filename = sprintf('workspace-portfolio-report-%s-%s.pdf', $workspace->id, now()->format('Ymd-His'));

                        return response()->streamDownload(
                            fn () => print ($pdf->output()),
                            $filename,
                        );
                    }),
                Action::make('startMeeting')
                    ->label('Start / Schedule Meeting')
                    ->icon('heroicon-m-video-camera')
                    ->color('primary')
                    ->form([
                        TextInput::make('title')
                            ->label('Meeting title')
                            ->required()
                            ->default(fn () => $project->name . ' meeting')
                            ->maxLength(255),
                        DateTimePicker::make('start_at')
                            ->label('Scheduled start')
                            ->helperText('Leave blank to start immediately'),
                        DateTimePicker::make('end_at')
                            ->label('Scheduled end')
                            ->after('start_at'),
                        Select::make('attendees')
                            ->label('Invite attendees')
                            ->multiple()
                            ->searchable()
                            ->options(
                                $project->users()
                                    ->where('users.id', '!=', filament()->auth()->id())
                                    ->orderBy('first_name')
                                    ->get(['users.id', 'first_name', 'last_name', 'email'])
                                    ->mapWithKeys(fn (User $user): array => [
                                        $user->id => trim($user->first_name.' '.$user->last_name).' ('.$user->email.')',
                                    ])
                                    ->all()
                            ),
                    ])
                    ->action(function (array $data) use ($project): void {
                        $meeting = Meeting::query()->create([
                            'project_id'   => $project->id,
                            'host_user_id' => filament()->auth()->id(),
                            'title'        => $data['title'],
                            'start_at'     => $data['start_at'] ?? null,
                            'end_at'       => $data['end_at'] ?? null,
                        ]);

                        $meeting->meetingUsers()->create([
                            'user_id'            => filament()->auth()->id(),
                            'invited_by_user_id' => filament()->auth()->id(),
                            'is_host'            => true,
                            'joined_at'          => now(),
                        ]);

                        foreach (($data['attendees'] ?? []) as $attendeeId) {
                            $meeting->meetingUsers()->firstOrCreate(
                                ['user_id' => (int) $attendeeId],
                                [
                                    'invited_by_user_id' => filament()->auth()->id(),
                                    'is_host'            => false,
                                ]
                            );
                        }

                        $this->redirect(MeetingResource::getUrl('go', [
                            'tenant' => filament()->getTenant()?->slug,
                            'project' => $project,
                            'record' => $meeting,
                        ]), navigate: true);
                    }),
            ])
                ->button()
                ->icon('heroicon-o-ellipsis-vertical')
                ->label('Actions')
                ->color('primary'),
            EditAction::make()
                ->color('gray'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BoardsKanbanWidget::make([
                'record' => $this->record,
            ]),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    public function getHeading(): string|Htmlable|null
    {
        return "{$this->record->name} ({$this->record->workspace->name})";
    }

    public function getSubheading(): string|Htmlable|null
    {
        return $this->record->description ? Str::limit($this->record->description, 150) : null;
    }

    /**
     * @return array<string, mixed>
     */
    private function buildReportData(Project $project): array
    {
        $categorizedTasks = [
            'pending'   => collect(),
            'ongoing'   => collect(),
            'review'    => collect(),
            'completed' => collect(),
            'other'     => collect(),
        ];

        $tasks = $project->tasks->map(function ($task): array {
            $boardName = $task->board?->name ?? 'Uncategorized';
            $statusKey = $this->resolveStatusKey($boardName);

            $lastComment = $this->resolveLatestComment($task);

            return [
                'id'           => $task->id,
                'title'        => $task->title,
                'status'       => $boardName,
                'status_key'   => $statusKey,
                'priority'     => Str::title((string) $task->priority),
                'assignee'     => $task->assignedUsers->pluck('name')->join(', ') ?: 'Unassigned',
                'start_at'     => $task->start_at,
                'end_at'       => $task->end_at,
                'last_comment' => $lastComment,
            ];
        });

        foreach ($tasks as $task) {
            $categorizedTasks[$task['status_key']]->push($task);
        }

        $statusSummary = collect($categorizedTasks)
            ->map(fn (Collection $bucket): int => $bucket->count())
            ->all();

        return [
            'status_summary'       => $statusSummary,
            'categorized_tasks'    => $categorizedTasks,
            'total_tasks'          => $tasks->count(),
            'project_last_comment' => $this->resolveLatestComment($project),
        ];
    }

    private function resolveStatusKey(string $boardName): string
    {
        $normalized = Str::of($boardName)->lower()->toString();

        return match (true) {
            str_contains($normalized, 'pending'), str_contains($normalized, 'todo'), str_contains($normalized, 'backlog') => 'pending',
            str_contains($normalized, 'ongoing'), str_contains($normalized, 'progress'), str_contains($normalized, 'active') => 'ongoing',
            str_contains($normalized, 'review') => 'review',
            str_contains($normalized, 'complete'), str_contains($normalized, 'done'), str_contains($normalized, 'closed') => 'completed',
            default => 'other',
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function buildPortfolioReportData(Workspace $workspace): array
    {
        $projects = $workspace->projects;
        $statusTotals = [
            'pending'   => 0,
            'ongoing'   => 0,
            'review'    => 0,
            'completed' => 0,
            'other'     => 0,
        ];

        $overdueTasks = collect();
        $riskTasks = collect();

        $projectRows = $projects->map(function (Project $project) use (&$statusTotals, &$overdueTasks, &$riskTasks): array {
            $tasks = $project->tasks;

            $counts = [
                'pending'   => 0,
                'ongoing'   => 0,
                'review'    => 0,
                'completed' => 0,
                'other'     => 0,
            ];

            foreach ($tasks as $task) {
                $boardName = $task->board?->name ?? 'Uncategorized';
                $statusKey = $this->resolveStatusKey($boardName);
                $counts[$statusKey]++;
                $statusTotals[$statusKey]++;

                $isCompleted = 'completed' === $statusKey;
                $isOverdue = ! $isCompleted && $task->end_at && $task->end_at->isPast();
                $isRisk = ! $isCompleted && ('high' === Str::lower((string) $task->priority) || $isOverdue);

                if ($isOverdue) {
                    $overdueTasks->push([
                        'project'  => $project->name,
                        'task'     => $task->title,
                        'assignee' => $task->assignedUsers->pluck('name')->join(', ') ?: 'Unassigned',
                        'end_at'   => $task->end_at,
                    ]);
                }

                if ($isRisk) {
                    $riskTasks->push([
                        'project'  => $project->name,
                        'task'     => $task->title,
                        'priority' => Str::title((string) $task->priority),
                        'status'   => $boardName,
                        'assignee' => $task->assignedUsers->pluck('name')->join(', ') ?: 'Unassigned',
                        'end_at'   => $task->end_at,
                    ]);
                }
            }

            return [
                'id'             => $project->id,
                'name'           => $project->name,
                'status'         => Str::title((string) $project->status),
                'task_count'     => $tasks->count(),
                'counts'         => $counts,
                'latest_comment' => $this->resolveLatestComment($project),
            ];
        });

        return [
            'projects'      => $projectRows,
            'project_count' => $projects->count(),
            'task_total'    => array_sum($statusTotals),
            'status_totals' => $statusTotals,
            'overdue_tasks' => $overdueTasks->sortBy('end_at')->values(),
            'risk_tasks'    => $riskTasks->sortBy('end_at')->values(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function buildExecutiveReportData(Project $project): array
    {
        $tasks = $project->tasks;

        $statusTotals = [
            'pending'   => 0,
            'ongoing'   => 0,
            'review'    => 0,
            'completed' => 0,
            'other'     => 0,
        ];

        foreach ($tasks as $task) {
            $boardName = $task->board?->name ?? 'Uncategorized';
            $statusTotals[$this->resolveStatusKey($boardName)]++;
        }

        $totalTasks = max($tasks->count(), 1);
        $completionRate = round((($statusTotals['completed'] ?? 0) / $totalTasks) * 100, 1);

        $overdueTasks = $tasks
            ->filter(function ($task): bool {
                $boardName = $task->board?->name ?? 'Uncategorized';
                $isCompleted = 'completed' === $this->resolveStatusKey($boardName);

                return ! $isCompleted && $task->end_at && $task->end_at->isPast();
            })
            ->values();

        $highRiskTasks = $tasks
            ->filter(function ($task): bool {
                $boardName = $task->board?->name ?? 'Uncategorized';
                $isCompleted = 'completed' === $this->resolveStatusKey($boardName);

                return ! $isCompleted && ('high' === Str::lower((string) $task->priority));
            })
            ->values();

        $upcomingMilestones = $tasks
            ->filter(function ($task): bool {
                $boardName = $task->board?->name ?? 'Uncategorized';
                $isCompleted = 'completed' === $this->resolveStatusKey($boardName);

                return ! $isCompleted && $task->end_at && $task->end_at->isFuture();
            })
            ->sortBy('end_at')
            ->take(5)
            ->values();

        return [
            'task_total'           => $tasks->count(),
            'status_totals'        => $statusTotals,
            'completion_rate'      => $completionRate,
            'overdue_count'        => $overdueTasks->count(),
            'high_risk_count'      => $highRiskTasks->count(),
            'upcoming_milestones'  => $upcomingMilestones,
            'project_last_comment' => $this->resolveLatestComment($project),
        ];
    }

    /**
     * @return array{author: string|null, body: string|null, created_at: \DateTimeInterface|null}|null
     */
    private function resolveLatestComment(Model $record): ?array
    {
        if (! method_exists($record, 'comments')) {
            return null;
        }

        $lastComment = $record->comments
            ->sortByDesc('created_at')
            ->first();

        if (! $lastComment) {
            return null;
        }

        return [
            'author'     => $lastComment->author?->name,
            'body'       => Str::of(strip_tags((string) $lastComment->body))->squish()->limit(220)->toString(),
            'created_at' => $lastComment->created_at,
        ];
    }

    private function resolveLogoDataUri(?string $path): ?string
    {
        if (blank($path) || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $absolutePath = Storage::disk('public')->path($path);
        $imageContents = @file_get_contents($absolutePath);

        if (false === $imageContents) {
            return null;
        }

        $mimeType = mime_content_type($absolutePath) ?: 'image/png';

        return 'data:'.$mimeType.';base64,'.base64_encode($imageContents);
    }
}
