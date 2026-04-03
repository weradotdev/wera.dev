<?php

namespace App\Models;

use Database\Factories\AgentConversationFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class AgentConversation extends Model
{
    /** @use HasFactory<AgentConversationFactory> */
    use HasFactory;

    protected $table = 'agent_conversations';

    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'user_id',
        'title',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $conversation): void {
            if (blank($conversation->id)) {
                $conversation->id = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(AgentConversationMessage::class, 'conversation_id')
            ->orderBy('created_at');
    }
}
