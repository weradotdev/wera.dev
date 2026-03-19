<?php

namespace App\Http\Requests;

use Orion\Http\Requests\Request;

class WorkspaceRequest extends Request
{
    public function storeRules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'slug'        => ['required', 'string', 'max:255', 'unique:workspaces,slug'],
            'icon'        => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'string', 'max:255'],
            'color'       => ['nullable', 'string', 'max:255'],
        ];
    }

    public function updateRules(): array
    {
        $key = $this->route('workspace');
        $id = $key && is_object($key) ? $key->getKey() : $key;

        return [
            'name'        => ['sometimes', 'string', 'max:255'],
            'slug'        => ['sometimes', 'string', 'max:255', 'unique:workspaces,slug,'.$id],
            'icon'        => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image'       => ['nullable', 'string', 'max:255'],
            'color'       => ['nullable', 'string', 'max:255'],
        ];
    }
}
