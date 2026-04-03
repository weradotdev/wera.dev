<?php

namespace App\Models;

use Database\Factories\AgentConversationMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AgentConversationMessage extends Model
{
    /** @use HasFactory<AgentConversationMessageFactory> */
    use HasFactory;

    protected $table = 'agent_conversation_messages';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'conversation_id',
        'user_id',
        'agent',
        'role',
        'content',
        'attachments',
        'tool_calls',
        'tool_results',
        'usage',
        'meta',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'user_id'      => 'integer',
            'attachments'  => 'array',
            'tool_calls'   => 'array',
            'tool_results' => 'array',
            'usage'        => 'array',
            'meta'         => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $message): void {
            if (blank($message->id)) {
                $message->id = (string) Str::uuid();
            }
        });
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(AgentConversation::class, 'conversation_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
