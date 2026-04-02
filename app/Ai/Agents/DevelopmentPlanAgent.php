<?php

namespace App\Ai\Agents;

use App\Ai\Tools\GetProjectDetails;
use App\Ai\Tools\GetProjectTasks;
use App\Ai\Tools\GetProjectUsers;
use App\Models\Project;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class DevelopmentPlanAgent implements Agent, Conversational, HasTools
{
    use Promptable;

    public function __construct(
        public Project $project
    ) {}

    public function instructions(): Stringable|string
    {
        return 'You are a development planning expert. Use the provided tools to fetch the project (id: '.$this->project->id.'), its tasks, and team members. '
            .'Then generate a clear, phased development plan with suggested tasks that align with the project description and goals. '
            .'Output a structured plan: phases or milestones, concrete task titles and short descriptions, and optional priority. '
            .'Base your plan only on the data you retrieve via the tools; do not invent project or task data.';
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
        ];
    }
}
