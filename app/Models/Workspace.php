<?php

namespace App\Models;

use Database\Factories\WorkspaceFactory;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Workspace extends Model implements HasAvatar
{
    /** @use HasFactory<WorkspaceFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'slug',
        'icon',
        'icon_dark',
        'description',
        'image',
        'image_dark',
        'color',
    ];

    protected $appends = ['icon_url', 'icon_dark_url', 'image_url', 'image_dark_url'];

    public function iconUrl(): Attribute
    {
        return Attribute::make(get: fn () => $this->icon ? asset("storage/$this->icon") : null);
    }

    public function iconDarkUrl(): Attribute
    {
        return Attribute::make(get: fn () => $this->icon_dark ? asset("storage/$this->icon_dark") : null);
    }

    public function imageUrl(): Attribute
    {
        return Attribute::make(get: fn () => $this->image ? asset("storage/$this->image") : null);
    }

    public function imageDarkUrl(): Attribute
    {
        return Attribute::make(get: fn () => $this->image_dark ? asset("storage/$this->image_dark") : null);
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function boards(): HasMany
    {
        return $this->hasMany(Board::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function workspaceUsers(): HasMany
    {
        return $this->hasMany(WorkspaceUser::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workspace_users')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if (blank($this->icon)) {
            return null;
        }

        return Storage::disk('public')->url($this->icon);
    }

    public function getFilamentName(): string
    {
        return $this->display_name ?? $this->name;
    }
}
