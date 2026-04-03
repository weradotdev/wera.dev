<?php

use App\Models\AgentConversation;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('creates a project assistant conversation and returns a status reply', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $project->users()->attach($user->id, ['role' => 'member']);

    Task::factory()->count(2)->create([
        'project_id'   => $project->id,
        'workspace_id' => $project->workspace_id,
    ])->each(fn (Task $task) => $task->assignedUsers()->attach($user->id, ['role' => 'owner']));

    Sanctum::actingAs($user);

    $response = $this->post(route('api.v1.projects.assistant.messages.store', ['project' => $project]), [
        'message' => 'project status',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Assistant response generated successfully.')
        ->assertJsonPath('conversation.project_id', $project->id)
        ->assertJsonPath('conversation.channel', 'mobile')
        ->assertJsonPath('reply.role', 'assistant');

    expect($response->json('reply.content'))->toContain($project->name);
    expect($response->json('conversation.messages'))->toHaveCount(2);

    expect(AgentConversation::query()->count())->toBe(1);
});

it('continues an existing conversation and keeps appending messages', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $project->users()->attach($user->id, ['role' => 'member']);

    $task = Task::factory()->create([
        'project_id'   => $project->id,
        'workspace_id' => $project->workspace_id,
        'end_at'       => now()->subDay(),
    ]);

    $task->assignedUsers()->attach($user->id, ['role' => 'owner']);

    Sanctum::actingAs($user);

    $firstResponse = $this->post(route('api.v1.projects.assistant.messages.store', ['project' => $project]), [
        'message' => 'help',
    ]);

    $conversationId = $firstResponse->json('conversation.id');

    $secondResponse = $this->post(route('api.v1.projects.assistant.messages.store', ['project' => $project]), [
        'message'         => 'overdue',
        'conversation_id' => $conversationId,
        'channel'         => 'whatsapp',
    ]);

    $secondResponse
        ->assertOk()
        ->assertJsonPath('conversation.id', $conversationId)
        ->assertJsonPath('reply.meta.channel', 'whatsapp');

    expect($secondResponse->json('reply.content'))->toContain('overdue');
    expect($secondResponse->json('conversation.messages'))->toHaveCount(4);
});

it('returns the latest assistant conversation for the current user and channel', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    $project->users()->attach($user->id, ['role' => 'member']);

    Sanctum::actingAs($user);

    $this->post(route('api.v1.projects.assistant.messages.store', ['project' => $project]), [
        'message' => 'project status',
    ])->assertOk();

    $response = $this->get(route('api.v1.projects.assistant.conversations.latest', ['project' => $project, 'channel' => 'mobile']));

    $response
        ->assertOk()
        ->assertJsonPath('conversation.project_id', $project->id)
        ->assertJsonCount(2, 'conversation.messages');
});

it('rejects assistant access for users outside the project', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create();

    Sanctum::actingAs($user);

    $response = $this->post(route('api.v1.projects.assistant.messages.store', ['project' => $project]), [
        'message' => 'project status',
    ]);

    $response
        ->assertForbidden()
        ->assertJsonPath('message', 'You do not have access to this project assistant.');
});
