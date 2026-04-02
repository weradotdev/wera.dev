<?php

namespace App\Http\Middleware;

use Illuminate\Support\Str;

class ValidateCsrfToken extends \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken
{
    /**
     * Check if the CSRF tokens match for the given request.
     *
     *
     * @return bool True if the CSRF tokens match, false otherwise.
     */
    protected function tokensMatch(mixed $request): bool
    {
        return Str::is('pages::ticket', $this->getLivewireComponentPath($request)) ?: parent::tokensMatch($request);
    }

    /**
     * Get Livewire component path from the request.
     */
    protected function getLivewireComponentPath(mixed $request): ?string
    {
        $components = $request->input('components')[0] ?? [];
        $snapshot = json_decode($components['snapshot'] ?? '{}', true);
        $memo = $snapshot['memo'] ?? [];

        return $memo['name'] ?? null;
    }
}
