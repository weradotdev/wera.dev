<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'workspace_id' => $this->workspace_id,
            'user_id'      => $this->user_id,
            'name'         => $this->name,
            'slug'         => $this->slug,
            'icon'         => $this->icon,
            'icon_url'     => $this->icon_url,
            'description'  => $this->description,
            'image'        => $this->image,
            'image_url'    => $this->image_url,
            'banner'       => $this->banner,
            'banner_url'   => $this->banner_url,
            'color'        => $this->color,
            'status'       => $this->status,
            'settings'     => $this->when($this->settings !== null, $this->settings),
            'start_date'   => $this->start_date?->format('Y-m-d'),
            'end_date'     => $this->end_date?->format('Y-m-d'),
            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),
            'workspace'    => new WorkspaceResource($this->whenLoaded('workspace')),
            'creator'      => new UserResource($this->whenLoaded('creator')),
            'boards'       => BoardResource::collection($this->whenLoaded('boards')),
            'tasks'        => TaskResource::collection($this->whenLoaded('tasks')),
            'tickets'      => TicketResource::collection($this->whenLoaded('tickets')),
            'users'        => UserResource::collection($this->whenLoaded('users')),
            'project_users' => ProjectUserResource::collection($this->whenLoaded('projectUsers')),
            'plans'        => PlanResource::collection($this->whenLoaded('plans')),
            'last_comment' => $this->last_comment
        ];
    }
}
