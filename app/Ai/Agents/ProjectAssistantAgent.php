<?php

namespace App\Ai\Agents;

use App\Ai\Tools\GetProjectDetails;
use App\Ai\Tools\GetProjectTasks;
use App\Ai\Tools\GetProjectUsers;
use App\Ai\Tools\GetTaskDetails;
use App\Models\Project;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class ProjectAssistantAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(
        public Project $project,
    ) {}

    public function instructions(): Stringable|string
    {
        return 'You are a concise project copilot for Wera. Always use the provided tools to inspect project '.$this->project->id.' before answering. '
            .'Answer only using data you can retrieve from tools. '
            .'Be specific, practical, and brief. '
            .'If the user asks for status, blockers, risks, priorities, or task ownership, summarize what the current project data supports. '
            .'If the data is insufficient, say so clearly instead of inventing details.';
    }

    /**
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [
            new GetProjectDetails,
            new GetProjectTasks,
            new GetProjectUsers,
            new GetTaskDetails,
        ];
    }
}
