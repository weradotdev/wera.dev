<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'workspace_id'  => $this->workspace_id,
            'user_id'       => $this->user_id,
            'planable_type' => $this->planable_type,
            'planable_id'   => $this->planable_id,
            'name'          => $this->name,
            'description'   => $this->description,
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),

            'planable' => $this->whenLoaded('planable', function () {
                $planable = $this->planable;
                if ($planable instanceof Project) {
                    return new ProjectResource($planable);
                }

                return $planable;
            }),
            'workspace' => new WorkspaceResource($this->whenLoaded('workspace')),
            'user'      => new UserResource($this->whenLoaded('user')),
            'revisions' => PlanRevisionResource::collection($this->whenLoaded('revisions')),
        ];
    }
}
