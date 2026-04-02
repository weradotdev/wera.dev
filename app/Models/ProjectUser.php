<?php

namespace App\Models;

use App\Traits\BelongsToProject;
use Database\Factories\ProjectUserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class ProjectUser extends Model
{
    /** @use HasFactory<ProjectUserFactory> */
    use BelongsToProject;

    use HasFactory;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'project_id',
        'user_id',
        'role',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tasks(): HasManyThrough
    {
        return $this->hasManyThrough(
            Task::class,
            TaskUser::class,
            'user_id',
            'id',
            'user_id',
            'task_id'
        )->where('tasks.project_id', $this->project_id);
    }
}
