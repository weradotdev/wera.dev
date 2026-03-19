<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanRevision extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'plan_id',
        'name',
        'description',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }
}
