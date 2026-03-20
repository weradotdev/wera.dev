<?php

namespace App\Filament\Admin\Resources\Projects\Pages;

use App\Filament\Admin\Resources\Projects\ProjectResource;
use App\Filament\Concerns\InteractsWithProjectMeetingRoom;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class DoProject extends Page
{
    use InteractsWithProjectMeetingRoom;
    use InteractsWithRecord;

    protected static string $resource = ProjectResource::class;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.resources.projects.pages.do-project';

    public function mount(int|string $record, ?string $meeting = null): void
    {
        $this->record = $this->resolveRecord($record);

        $this->initializeMeetingRoom($meeting);
    }
}
