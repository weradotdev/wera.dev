<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Plan extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'workspace_id',
        'user_id',
        'planable_type',
        'planable_id',
        'name',
        'description',
    ];

    public function planable(): MorphTo
    {
        return $this->morphTo();
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(PlanRevision::class)->orderByDesc('created_at');
    }
}
