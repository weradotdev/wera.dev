<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kirschbaum\Commentions\Comment;
use Orion\Http\Controllers\RelationController as Controller;

class ProjectCommentController extends Controller
{
    protected $model = Project::class;

    protected $relation = 'comments';

    protected function buildParentFetchQuery(Request $request, $parentKey): Builder
    {
        return Project::query()->forUser(Auth::id());
    }

    public function filterableBy(): array
    {
        return [
            'id',
            'body',
            'author_id',
            'created_at',
            'updated_at',
            'author.id',
            'author.name',
        ];
    }

    public function sortableBy(): array
    {
        return ['id', 'created_at', 'updated_at'];
    }

    public function searchableBy(): array
    {
        return ['body'];
    }

    public function includes(): array
    {
        return ['author'];
    }

    public function alwaysIncludes(): array
    {
        return ['author'];
    }

    protected function buildRelationFetchQuery(Request $request, Model $parentEntity, array $requestedRelations): Relation
    {
        return parent::buildRelationFetchQuery($request, $parentEntity, $requestedRelations)
            ->oldest('created_at');
    }

    protected function beforeSave(Request $request, Model $entity)
    {
        if ($entity instanceof Comment) {
            $entity->author_type = Auth::user()?->getMorphClass();
            $entity->author_id = Auth::id();
        }

        return parent::beforeSave($request, $entity);
    }

    public function aggregates(): array
    {
        return ['author'];
    }
}
