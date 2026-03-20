<?php

namespace App\Models;

use Database\Factories\MeetingFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Meeting extends Model
{
    /** @use HasFactory<MeetingFactory> */
    use HasFactory, HasUlids;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'host_user_id',
        'title',
        'status',
        'started_at',
        'ended_at',
        'meta',
    ];

    protected $appends = [
        'computed_status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta'       => 'array',
            'started_at' => 'datetime',
            'ended_at'   => 'datetime',
        ];
    }

    public function computedStatus(): Attribute
    {
        return Attribute::make(get: function (): string {
            if ($this->ended_at !== null || in_array($this->status, ['ended', 'cancelled'], true)) {
                return 'past';
            }

            if ($this->started_at !== null) {
                return $this->started_at->isFuture() ? 'upcoming' : 'ongoing';
            }

            return match ($this->status) {
                'live' => 'ongoing',
                'scheduled' => 'upcoming',
                default => 'past',
            };
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function meetingUsers(): HasMany
    {
        return $this->hasMany(MeetingUser::class);
    }

    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_users')
            ->withPivot(['invited_by_user_id', 'is_host', 'joined_at', 'left_at'])
            ->withTimestamps();
    }
}
