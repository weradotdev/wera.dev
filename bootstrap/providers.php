<?php

use App\Providers\AppServiceProvider;
use App\Providers\EventServiceProvider;
use App\Providers\Filament\AdminPanelProvider;
use App\Providers\Filament\DeveloperPanelProvider;
use App\Providers\Filament\DocsPanelProvider;

return [
    AppServiceProvider::class,
    EventServiceProvider::class,
    AdminPanelProvider::class,
    DeveloperPanelProvider::class,
    DocsPanelProvider::class,
];
