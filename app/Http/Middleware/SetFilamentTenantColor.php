<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetFilamentTenantColor
{
    private string $defaultPrimary = '#0097b2';

    public function handle(Request $request, Closure $next): Response
    {
        $tenant = Filament::getTenant();

        $hex = $tenant && filled($tenant->color ?? null)
            ? $tenant->color
            : $this->defaultPrimary;

        FilamentColor::register([
            'primary' => Color::hex($hex),
        ]);

        return $next($request);
    }
}