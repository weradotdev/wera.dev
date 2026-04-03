<?php

namespace App\Http\Controllers;

use App\Http\Resources\AgentConversationMessageResource;
use App\Http\Resources\ProjectConversationResource;
use App\Models\AssistantActionRequest;
use App\Models\Project;
use App\Models\ProjectConversation;
use App\Services\Assistant\ProjectAssistantOrchestrator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AssistantController extends Controller
{
    public function latest(Request $request, Project $project, ProjectAssistantOrchestrator $assistant): JsonResponse
    {
        $channel = $request->query('channel', 'mobile');

        $conversation = $assistant->latestConversation(
            project: $project,
            user: $request->user(),
            channel: is_string($channel) ? $channel : 'mobile',
        );

        return response()->json([
            'conversation' => $conversation ? new ProjectConversationResource($conversation) : null,
        ]);
    }

    public function store(Request $request, Project $project, ProjectAssistantOrchestrator $assistant): JsonResponse
    {
        $payload = $request->validate([
            'message'         => ['required', 'string', 'max:5000'],
            'conversation_id' => ['nullable', 'string', 'size:36'],
            'channel'         => ['nullable', 'string', 'max:50'],
        ]);

        $projectConversation = filled($payload['conversation_id'] ?? null)
            ? ProjectConversation::query()->findOrFail($payload['conversation_id'])
            : null;

        $result = $assistant->respond(
            project: $project,
            user: $request->user(),
            message: $payload['message'],
            projectConversation: $projectConversation,
            channel: $payload['channel'] ?? 'mobile',
        );

        return response()->json([
            'message'      => 'Assistant response generated successfully.',
            'conversation' => new ProjectConversationResource($result['conversation']),
            'reply'        => new AgentConversationMessageResource($result['reply']),
        ]);
    }

    public function confirmAction(Request $request, Project $project, AssistantActionRequest $actionRequest, ProjectAssistantOrchestrator $assistant): JsonResponse
    {
        if ((int) $actionRequest->project_id !== $project->id || (int) $actionRequest->user_id !== $request->user()->id) {
            abort(403);
        }

        $replyPayload = $assistant->confirmAction($actionRequest);

        return response()->json([
            'message' => $replyPayload['content'],
            'mode'    => $replyPayload['mode'],
        ]);
    }
}
