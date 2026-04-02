<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Tenancy\EditTenantProfile;
use App\Filament\Admin\Pages\Tenancy\RegisterTenant;
use App\Filament\Admin\Resources\Workspaces\WorkspaceResource;
use App\Http\Middleware\SetFilamentTenantColor;
use App\Models\User;
use App\Models\Workspace;
use DutchCodingCompany\FilamentSocialite\FilamentSocialitePlugin;
use DutchCodingCompany\FilamentSocialite\Provider;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Guava\FilamentKnowledgeBase\Plugins\KnowledgeBaseCompanionPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Laravel\Socialite\Contracts\User as SocialiteUserContract;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('admin')
            ->path('')
            ->domain(config('app.domains.workspace', 'ws.wera.dev'))
            ->viteTheme([
                'resources/css/filament/admin/theme.css',
            ])
            ->tenant(Workspace::class, slugAttribute: 'slug')
            ->tenantRegistration(RegisterTenant::class)
            ->tenantProfile(EditTenantProfile::class)
            ->tenantMiddleware([
                SetFilamentTenantColor::class,
            ])
            ->tenantMenuItems([
                Action::make('workspaces')
                    ->label('All Workspaces')
                    ->url(fn (): string => WorkspaceResource::getUrl('index'))
                    ->icon('heroicon-m-cog-8-tooth'),
            ])
            ->login()
            ->registration()
            ->profile(isSimple: false)
            ->passwordReset()
            ->databaseNotifications()
            ->searchableTenantMenu()
            ->colors([
                'primary' => Color::hex('#0097b2'),
            ])
            ->font('IBM Plex Mono')
            ->topbar(false)
            ->sidebarWidth('12rem')
            ->brandLogoHeight('2rem')
            ->brandLogo(asset('logo.png'))
            ->favicon(asset('favicon.png'))
            ->sidebarCollapsibleOnDesktop()
            ->maxContentWidth(Width::Full)
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\Filament\Admin\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\Filament\Admin\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\Filament\Admin\Widgets')
            ->widgets([])
            ->plugins([
                // KnowledgeBaseCompanionPlugin::make()
                //     ->knowledgeBasePanelId('docs')
            ])
            ->plugin(
                FilamentSocialitePlugin::make()
                    ->providers([
                        Provider::make('github')
                            ->label('GitHub')
                            ->icon('hugeicons-github')
                            ->color(Color::hex('#6e5494'))
                            ->outlined(true),
                        Provider::make('google')
                            ->label('Google')
                            ->icon('hugeicons-google')
                            ->color(Color::hex('#ea4335'))
                            ->outlined(true),
                    ])
                    ->registration(true)
                    ->createUserUsing(function (string $provider, SocialiteUserContract $oauthUser, FilamentSocialitePlugin $plugin): User {
                        return User::query()->create([
                            'name'     => $oauthUser->getName() ?? $oauthUser->getEmail(),
                            'email'    => $oauthUser->getEmail(),
                            'phone'    => 'oauth-'.$provider.'-'.$oauthUser->getId(),
                            'password' => null,
                            'type'     => 'project_manager',
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
