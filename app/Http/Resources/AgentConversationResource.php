<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentConversationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'channel'    => $this->channel,
            'user_id'    => $this->user_id,
            'project_id' => $this->project_id,
            'task_id'    => $this->task_id,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'messages'   => AgentConversationMessageResource::collection($this->whenLoaded('messages')),
        ];
    }
}
