<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Support\Str;

class ProjectConversation extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'project_id',
        'user_id',
        'task_id',
        'channel',
        'conversation_id',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model): void {
            if (blank($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AgentConversation::class, 'conversation_id');
    }

    /**
     * All messages that belong to the underlying agent conversation.
     */
    public function messages(): HasManyThrough
    {
        return $this->hasManyThrough(
            AgentConversationMessage::class,
            AgentConversation::class,
            'id',              // FK on AgentConversation matched to conversation_id on this model
            'conversation_id', // FK on AgentConversationMessage
            'conversation_id', // local key on ProjectConversation
            'id',              // local key on AgentConversation
        );
    }
}
