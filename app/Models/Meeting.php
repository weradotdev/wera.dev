<?php

namespace App\Models;

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
        'user_id',
        'title',
        'start_at',
        'end_at',
        'started_at',
        'ended_at',
        'meta',
    ];

    protected $appends = [
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta'       => 'array',
            'start_at'   => 'datetime',
            'end_at'     => 'datetime',
            'started_at' => 'datetime',
            'ended_at'   => 'datetime',
        ];
    }

    public function status(): Attribute
    {
        return Attribute::make(get: function (): string {
            // Meeting has actually ended
            if (null !== $this->ended_at) {
                return 'past';
            }

            // Meeting is live (actual start recorded, not yet ended)
            if (null !== $this->started_at) {
                return 'ongoing';
            }

            // Not yet started — derive from schedule
            if (null !== $this->start_at) {
                return $this->start_at->isFuture() ? 'upcoming' : 'ongoing';
            }

            return 'upcoming';
        });
    }

    /**
     * Summary of project
     * @return BelongsTo<Project, Meeting>
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Summary of host
     * @return BelongsTo<User, Meeting>
     */
    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function meetingUsers(): HasMany
    {
        return $this->hasMany(MeetingUser::class);
    }

    /**
     * Summary of attendees
     * @return BelongsToMany<User, Meeting>
     */
    public function attendees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_users')
            ->withPivot(['invited_by_user_id', 'is_host', 'joined_at', 'left_at'])
            ->withTimestamps();
    }
}
