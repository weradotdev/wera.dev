<?php

namespace App\Http\Requests;

use Orion\Http\Requests\Request;

class UserRequest extends Request
{
    public function storeRules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['required', 'string', 'max:255', 'unique:users,phone'],
            'type'     => ['nullable', 'string', 'max:255', 'in:developer,admin,project_manager'],
            'avatar'   => ['nullable', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function updateRules(): array
    {
        $key = $this->route('user');
        $id = $key && is_object($key) ? $key->getKey() : $key;

        return [
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            'phone'    => ['sometimes', 'string', 'max:255', 'unique:users,phone,'.$id],
            'type'     => ['nullable', 'string', 'max:255', 'in:developer,admin,project_manager'],
            'avatar'   => ['nullable', 'string', 'max:255'],
            'password' => ['sometimes', 'nullable', 'string', 'min:8', 'confirmed'],
        ];
    }
}
