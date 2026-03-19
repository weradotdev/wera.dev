<?php

namespace App\Models;

use App\Traits\BelongsToWorkspace;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Kirschbaum\Commentions\Comment;
use Kirschbaum\Commentions\Contracts\Commentable;
use Kirschbaum\Commentions\HasComments;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Project extends Model implements Commentable, HasAvatar, HasMedia
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use BelongsToWorkspace;

    use HasComments;
    use HasFactory;
    use InteractsWithMedia;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'workspace_id',
        'user_id',
        'name',
        'slug',
        'icon',
        'description',
        'image',
        'banner',
        'color',
        'status',
        'settings',
        'start_date',
        'end_date',
    ];

    protected $appends = ['icon_url', 'image_url', 'banner_url', 'last_comment'];

    public function iconUrl(): Attribute
    {
        return Attribute::make(get: fn() => $this->icon ? asset("storage/$this->icon") : null);
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(get: fn() => $this->image ? asset("storage/$this->image") : null);
    }

    public function bannerUrl(): Attribute
    {
        return Attribute::make(get: fn() => $this->banner ? asset("storage/$this->banner") : null);
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::created(function (Project $project): void {
            $project->ensureDefaultBoards();
        });
    }

    public function ensureDefaultBoards(): void
    {
        $defaultBoardNames = ['Pending', 'Ongoing', 'Review', 'Completed'];
        $colors            = ['#ef4444', '#3b82f6', '#eab308', '#22c55e'];

        foreach ($defaultBoardNames as $position => $name) {
            $this->boards()->firstOrCreate(
                ['name' => $name],
                ['position' => $position, 'color' => $colors[$position]],
            );
        }
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('screenshots');
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (blank($this->icon)) {
            return null;
        }

        return Storage::disk('public')->url($this->icon);
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'settings'   => 'array',
            'start_date' => 'date',
            'end_date'   => 'date',
        ];
    }



    /**
     * Default project settings structure for integrations and notifications.
     *
     * @return array{github: array{connected: bool, repo_url: string, create_issues_with_tasks: bool}, notifications: array{notify_developer_per_task: bool, channels: array<int, string>}, slack: array{connected: bool, webhook_url: string, channel: string}, telegram: array{connected: bool, bot_token: string}, whatsapp: array{connected: bool, session_id: string, has_group: bool, group_name: string, group_jid: string}}
     */
    public static function defaultSettings(): array
    {
        return [
            'github'        => [
                'connected'                => false,
                'repo_url'                 => '',
                'create_issues_with_tasks' => false,
            ],
            'notifications' => [
                'notify_developer_per_task' => false,
                'channels'                  => ['whatsapp'],
            ],
            'slack'         => [
                'connected'   => false,
                'webhook_url' => '',
                'channel'     => '',
            ],
            'telegram'      => [
                'connected' => false,
                'bot_token' => '',
                'chat_id'   => '',
            ],
            'whatsapp'      => [
                'connected'  => false,
                'session_id' => '',
                'has_group'  => false,
                'group_name' => '',
                'group_jid'  => '',
            ],
        ];
    }

    /**
     * Get settings merged with defaults so all integration keys exist.
     *
     * @return array<string, mixed>
     */
    public function getSettingsAttribute(mixed $value): array
    {
        $stored = is_array($value) ? $value : (is_string($value) ? json_decode($value, true) ?? [] : []);

        return array_replace_recursive(static::defaultSettings(), $stored);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function boards(): HasMany
    {
        return $this->hasMany(Board::class)->orderBy('position');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function projectUsers(): HasMany
    {
        return $this->hasMany(ProjectUser::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'project_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function plans(): MorphMany
    {
        return $this->morphMany(Plan::class, 'planable');
    }

    public function lastComment(): Attribute
    {
        return Attribute::make(get: function (): ?Comment {
            /** @var Comment|null $comment */
            $comment = $this->comments()->latest()->first();

            return $comment;
        });
    }

    /**
     * Scope to projects the given user (or the authenticated user) belongs to.
     */
    #[Scope]
    public function forUser(Builder $query, ?int $userId = null): void
    {
        $userId ??= Auth::id();

        if ($userId === null) {
            return;
        }

        $query->whereRelation('projectUsers', 'user_id', $userId);
    }
}
