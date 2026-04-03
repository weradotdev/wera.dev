<?php

namespace App\Http\Resources;

use App\Models\ProjectConversation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectConversationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var ProjectConversation $this */
        $conversation = $this->whenLoaded('conversation');

        return [
            'id'              => $this->id,
            'project_id'      => $this->project_id,
            'user_id'         => $this->user_id,
            'task_id'         => $this->task_id,
            'channel'         => $this->channel,
            'conversation_id' => $this->conversation_id,
            'title'           => $this->when($conversation, fn () => $this->conversation->title),
            'created_at'      => $this->created_at?->toIso8601String(),
            'updated_at'      => $this->updated_at?->toIso8601String(),
            'messages'        => $this->when(
                $conversation && $this->conversation->relationLoaded('messages'),
                fn () => AgentConversationMessageResource::collection($this->conversation->messages)
            ),
        ];
    }
}
