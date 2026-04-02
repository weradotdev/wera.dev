<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'type'              => $this->type,
            'avatar'            => $this->avatar,
            'email_verified_at' => $this->email_verified_at?->toIso8601String(),
            'created_at'        => $this->created_at?->toIso8601String(),
            'updated_at'        => $this->updated_at?->toIso8601String(),

            'workspaces'      => WorkspaceResource::collection($this->whenLoaded('workspaces')),
            'projects'        => ProjectResource::collection($this->whenLoaded('projects')),
            'assigned_tasks'  => TaskResource::collection($this->whenLoaded('assignedTasks')),
            'workspace_users' => WorkspaceUserResource::collection($this->whenLoaded('workspaceUsers')),
            'project_users'   => ProjectUserResource::collection($this->whenLoaded('projectUsers')),
            'task_users'      => TaskUserResource::collection($this->whenLoaded('taskUsers')),
        ];
    }
}
