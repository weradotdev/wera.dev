<?php

namespace App\Providers;

use App\Channels\Sms;
use App\Channels\WhatsApp;
use App\Events\DevelopmentPlanGenerated;
use App\Events\GenerateDevelopmentPlanRequested;
use App\Events\GenerateProgressReportRequested;
use App\Events\ProgressReportGenerated;
use App\Http\Scramble\OrionResponseOperationExtension;
use App\Listeners\PromptDevelopmentPlanAgent;
use App\Listeners\PromptProgressReportAgent;
use App\Listeners\StoreDevelopmentPlanRevision;
use App\Models\User;
use Dedoc\Scramble\Configuration\OperationTransformers;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\Listeners\ShareProgressReportWithProjectUsers;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Dedoc\Scramble\Scramble;
use Dedoc\Scramble\Support\Generator\OpenApi;
use Dedoc\Scramble\Support\Generator\SecurityScheme;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Scramble::ignoreDefaultRoutes();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(GenerateDevelopmentPlanRequested::class, PromptDevelopmentPlanAgent::class);
        Event::listen(DevelopmentPlanGenerated::class, StoreDevelopmentPlanRevision::class);
        Event::listen(GenerateProgressReportRequested::class, PromptProgressReportAgent::class);
        Event::listen(ProgressReportGenerated::class, ShareProgressReportWithProjectUsers::class);

        Blade::anonymousComponentPath(
            resource_path('views/filament/widgets/kanban/components'),
            'filament-kanban',
        );

        Livewire::component(
            'filament.widgets.add-task-kanban-form',
            \App\Filament\Widgets\AddTaskKanbanForm::class
        );

        Notification::extend('sms', fn () => new Sms());
        Notification::extend('whatsapp', fn () => new WhatsApp());

        Gate::define('viewApiDocs', fn ($user) => true);

        Gate::before(fn (User $user, string $ability) => $user->type == 'admin' ? true : null);

        // Gate::policy(Role::class, RolePolicy::class);
        // Gate::policy(Permission::class, PermissionPolicy::class);

        if ('local' === config('app.env')) {
            DB::listen(fn ($query) => Log::info($query->sql, ['Bindings' => $query->bindings, 'Time' => $query->time]));
        }

        Scramble::registerApi('v1', ['api_path' => 'v1', 'info' => ['version' => '1.0']]);

        Scramble::configure()
            ->withDocumentTransformers(function (OpenApi $openApi) {
                $openApi->secure(
                    SecurityScheme::http('bearer')
                );
            });

        Scramble::configure()->withOperationTransformers(
            function (OperationTransformers $transformers): void {
                $transformers->append(OrionResponseOperationExtension::class);
            }
        );

        Page::$reportValidationErrorUsing = function (ValidationException $exception) {
            FilamentNotification::make()
                ->title($exception->getMessage())
                ->danger()
                ->send();
        };

    }
}
