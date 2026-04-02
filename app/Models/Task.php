<?php

namespace App\Models;

use App\Observers\TaskObserver;
use App\Traits\BelongsToProject;
use App\Traits\BelongsToWorkspace;
use Database\Factories\TaskFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Kirschbaum\Commentions\Contracts\Commentable;
use Kirschbaum\Commentions\HasComments;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

#[ObservedBy(TaskObserver::class)]
class Task extends Model implements Commentable, HasMedia
{
    /** @use HasFactory<TaskFactory> */
    use BelongsToProject;

    use BelongsToWorkspace;
    use HasComments;
    use HasFactory;
    use InteractsWithMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'workspace_id',
        'project_id',
        'user_id',
        'board_id',
        'ticket_id',
        'title',
        'description',
        'priority',
        'checklist',
        'completed',
        'event_period',
        'due_at',
        'start_at',
        'end_at',
        'position',
    ];

    /**
     * @var list<string>
     */
    protected $appends = [
        'event_period',
        'progress',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'checklist' => 'array',
            'completed' => 'array',
            'due_at'    => 'datetime',
            'start_at'  => 'datetime',
            'end_at'    => 'datetime',
            'position'  => 'integer',
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('screenshots');
    }

    protected function progress(): Attribute
    {
        return Attribute::make(
            get: function (): int {
                $checklist = $this->checklist ?? [];
                if (empty($checklist) || ! is_array($checklist)) {
                    return 0;
                }
                $total = count($checklist);
                $completed = $this->completed ?? [];
                $completedCount = is_array($completed) ? count($completed) : 0;

                return $total > 0 ? (int) round(($completedCount / $total) * 100) : 0;
            },
        );
    }

    protected function eventPeriod(): Attribute
    {
        return Attribute::make(
            get: fn ($value, array $attributes): array => [
                'start' => $attributes['start_at'] ?? null,
                'end'   => $attributes['end_at'] ?? null,
            ],
            set: fn (?array $value): array => [
                'start_at' => filled($value['start'] ?? null) ? Carbon::parse($value['start']) : null,
                'end_at'   => filled($value['end'] ?? null) ? Carbon::parse($value['end']) : null,
            ],
        );
    }

    public function board(): BelongsTo
    {
        return $this->belongsTo(Board::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function taskUsers(): HasMany
    {
        return $this->hasMany(TaskUser::class);
    }

    public function assignedUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'task_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    /**
     * Scope to tasks assigned to the given user (or the authenticated user).
     */
    #[Scope]
    private function forUser(Builder $query, ?int $userId = null): void
    {
        $userId ??= Auth::id();

        if (null === $userId) {
            return;
        }

        $query->whereRelation('projectUsers', 'user_id', $userId);
    }

    /**
     * Scope to tasks for the given project
     */
    #[Scope]
    private function forProject(Builder $query, int $projectId): void
    {
        $query->where('project_id', $projectId);
    }
}
