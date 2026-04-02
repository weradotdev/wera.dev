<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'workspace_id' => $this->workspace_id,
            'project_id'   => $this->project_id,
            'user_id'      => $this->user_id,
            'board_id'     => $this->board_id,
            'ticket_id'    => $this->ticket_id,
            'title'        => $this->title,
            'description'  => $this->description,
            'priority'     => $this->priority,
            'checklist'    => $this->checklist,
            'completed'    => $this->completed,
            'event_period' => $this->event_period,
            'progress'     => $this->progress,
            'start_at'     => $this->start_at?->toIso8601String(),
            'end_at'       => $this->end_at?->toIso8601String(),
            'position'     => $this->position,
            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),

            'workspace'      => new WorkspaceResource($this->whenLoaded('workspace')),
            'project'        => new ProjectResource($this->whenLoaded('project')),
            'creator'        => new UserResource($this->whenLoaded('creator')),
            'board'          => new BoardResource($this->whenLoaded('board')),
            'ticket'         => new TicketResource($this->whenLoaded('ticket')),
            'assigned_users' => UserResource::collection($this->whenLoaded('assignedUsers')),
            'task_users'     => TaskUserResource::collection($this->whenLoaded('taskUsers')),
        ];
    }
}
