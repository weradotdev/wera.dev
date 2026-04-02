<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'icon'        => $this->icon,
            'description' => $this->description,
            'image'       => $this->image,
            'color'       => $this->color,
            'created_at'  => $this->created_at?->toIso8601String(),
            'updated_at'  => $this->updated_at?->toIso8601String(),

            'projects'        => ProjectResource::collection($this->whenLoaded('projects')),
            'boards'          => BoardResource::collection($this->whenLoaded('boards')),
            'tasks'           => TaskResource::collection($this->whenLoaded('tasks')),
            'tickets'         => TicketResource::collection($this->whenLoaded('tickets')),
            'users'           => UserResource::collection($this->whenLoaded('users')),
            'workspace_users' => WorkspaceUserResource::collection($this->whenLoaded('workspaceUsers')),
        ];
    }
}
