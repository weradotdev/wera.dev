<?php

namespace App\Http\Requests;

use Orion\Http\Requests\Request;

class TaskRequest extends Request
{
    public function storeRules(): array
    {
        return [
            'workspace_id' => ['required', 'integer', 'exists:workspaces,id'],
            'project_id'   => ['required', 'integer', 'exists:projects,id'],
            'user_id'      => ['nullable', 'integer', 'exists:users,id'],
            'board_id'     => ['nullable', 'integer', 'exists:boards,id'],
            'ticket_id'    => ['nullable', 'integer', 'exists:tickets,id'],
            'title'        => ['required', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'priority'      => ['nullable', 'string', 'max:255'],
            'checklist'    => ['nullable', 'array'],
            'completed'    => ['nullable', 'array'],
            'event_period' => ['nullable', 'array'],
            'start_at'     => ['nullable', 'date'],
            'end_at'       => ['nullable', 'date', 'after_or_equal:start_at'],
            'position'     => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function updateRules(): array
    {
        return [
            'workspace_id' => ['sometimes', 'integer', 'exists:workspaces,id'],
            'project_id'   => ['sometimes', 'integer', 'exists:projects,id'],
            'user_id'      => ['nullable', 'integer', 'exists:users,id'],
            'board_id'     => ['nullable', 'integer', 'exists:boards,id'],
            'ticket_id'    => ['nullable', 'integer', 'exists:tickets,id'],
            'title'        => ['sometimes', 'string', 'max:255'],
            'description'  => ['nullable', 'string'],
            'priority'     => ['nullable', 'string', 'max:255'],
            'checklist'    => ['nullable', 'array'],
            'completed'    => ['nullable', 'array'],
            'event_period' => ['nullable', 'array'],
            'start_at'     => ['nullable', 'date'],
            'end_at'       => ['nullable', 'date', 'after_or_equal:start_at'],
            'position'     => ['nullable', 'integer', 'min:0'],
        ];
    }
}
