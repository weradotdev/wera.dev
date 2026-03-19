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
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class ProgressReportAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(
        public Project $project
    ) {}

    public function instructions(): Stringable|string
    {
        return 'You are a project progress analyst. Use the provided tools to fetch the project (id: '.$this->project->id.'), its tasks (with progress and assignees), and team members. '
            .'Then write a concise, professional progress report suitable for sharing with the project team. '
            .'Include: summary of overall progress, completed vs in-progress vs pending work, notable blockers or risks if evident from task data, and a short next-steps section. '
            .'Base the report only on the data you retrieve via the tools.';
    }

    /**
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * @return \Laravel\Ai\Contracts\Tool[]
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
