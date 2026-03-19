<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkspaceUserResource extends JsonResource
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
            'role'         => $this->role,
            'created_at'   => $this->created_at?->toIso8601String(),
            'updated_at'   => $this->updated_at?->toIso8601String(),

            'workspace' => new WorkspaceResource($this->whenLoaded('workspace')),
            'user'      => new UserResource($this->whenLoaded('user')),
        ];
    }
}
