<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgentConversationMessageResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'conversation_id' => $this->conversation_id,
            'user_id'         => $this->user_id,
            'agent'           => $this->agent,
            'role'            => $this->role,
            'content'         => $this->content,
            'meta'            => $this->meta ?? [],
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
            'user'            => new UserResource($this->whenLoaded('user')),
        ];
    }
}
