<?php

use App\Models\AgentConversation;
use App\Models\Project;
use App\Models\ProjectConversation;
use App\Models\Task;
use App\Models\User;
use App\Services\WhatsAppCommandHandler;

it('routes whatsapp assistant messages through the shared assistant orchestrator', function () {
    $user = User::factory()->create([
        'phone' => '+15550001111',
    ]);

    $project = Project::factory()->create();
    $project->users()->attach($user->id, ['role' => 'member']);

    $task = Task::factory()->create([
        'project_id'   => $project->id,
        'workspace_id' => $project->workspace_id,
        'end_at'       => now()->subDay(),
    ]);

    $task->assignedUsers()->attach($user->id, ['role' => 'owner']);

    $response = $this->post(route('api.whatsapp.incoming'), [
        'session_id' => 'project-'.$project->id,
        'from'       => '+1 (555) 000-1111',
        'message'    => 'wera overdue',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('reply', "You have *1* overdue task(s) in *{$project->name}*.");

    $conversation = ProjectConversation::query()->first();

    expect($conversation)->not->toBeNull();
    expect($conversation?->channel)->toBe('whatsapp');
    expect($conversation?->project_id)->toBe($project->id);
    expect($conversation?->messages()->count())->toBe(2);
});

it('returns help text for whatsapp assistant messages from unknown users', function () {
    $project = Project::factory()->create();

    $response = $this->post(route('api.whatsapp.incoming'), [
        'session_id' => 'project-'.$project->id,
        'from'       => '+1 (555) 222-3333',
        'message'    => 'wera help',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('reply', app(WhatsAppCommandHandler::class)->help());

    expect(AgentConversation::query()->count())->toBe(0);
});
