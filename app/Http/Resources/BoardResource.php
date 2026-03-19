<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'project_id'  => $this->project_id,
            'name'        => $this->name,
            'color'       => $this->color,
            'position'    => $this->position,
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),

            'project' => new ProjectResource($this->whenLoaded('project')),
            'tasks'   => TaskResource::collection($this->whenLoaded('tasks')),
        ];
    }
}
