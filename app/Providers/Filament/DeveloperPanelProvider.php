<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Tenancy\RegisterProjectTenant;
use App\Http\Middleware\SetFilamentTenantColor;
use App\Models\Project;
use App\Models\User;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

class DeveloperPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('developer')
            ->path('')
            ->domain(config('app.domains.developer', 'app.wera.dev'))
            ->viteTheme('resources/css/filament/developer/theme.css')
            ->login()
            ->profile(isSimple: false)
            ->passwordReset()
            ->databaseNotifications()
            ->breadcrumbs(false)
            ->tenant(Project::class, 'slug')
            ->tenantRegistration(RegisterProjectTenant::class)
            ->tenantMiddleware([
                SetFilamentTenantColor::class,
            ])
            ->searchableTenantMenu()
            ->colors([
                'primary' => Color::hex('#0097b2'),
            ])
            ->font('IBM Plex Mono')
            ->topNavigation()
            ->brandLogoHeight('2rem')
            ->brandLogo(asset('logo.png'))
            ->favicon(asset('favicon.png'))
            ->maxContentWidth(Width::Full)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->plugin(
                FilamentSocialitePlugin::make()
                    ->providers([
                        Provider::make('github')
                            ->label('GitHub')
                            ->icon('hugeicons-github')
                            ->color(Color::hex('#24292f'))
                            ->outlined(true),
                        Provider::make('google')
                            ->label('Google')
                            ->icon('hugeicons-google')
                            ->color(Color::hex('#4285f4'))
                            ->outlined(true),
                    ])
                    ->registration(true)
                    ->createUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin): User {
                        return User::query()->create([
                            'name'     => $oauthUser->getName() ?? $oauthUser->getEmail(),
                            'email'    => $oauthUser->getEmail(),
                            'phone'    => 'oauth-'.$provider.'-'.$oauthUser->getId(),
                            'password' => null,
                            'type'     => 'developer',
                        ]);
                    })
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->spa();
    }
}
