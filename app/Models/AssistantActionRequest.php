<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AssistantActionRequest extends Model
{
    protected $table = 'assistant_action_requests';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'project_id',
        'user_id',
        'conversation_id',
        'channel',
        'action',
        'parameters',
        'status',
        'confirmation_code',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'parameters' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $request): void {
            if (blank($request->id)) {
                $request->id = (string) Str::uuid();
            }
            if (blank($request->expires_at)) {
                $request->expires_at = now()->addHour();
            }
            if (blank($request->confirmation_code)) {
                $request->confirmation_code = (string) random_int(1000, 9999);
            }
        });
    }

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? true;
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AgentConversation::class, 'conversation_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '>', now());
    }
}
