<?php

namespace App\Filament\Admin\Resources\Projects\Resources\Meetings\Pages;

use App\Filament\Admin\Resources\Projects\Resources\Meetings\MeetingResource;
use App\Filament\Concerns\InteractsWithMeetingRoom;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;
use Filament\Resources\Pages\Page;

class GoMeeting extends Page
{
    use InteractsWithMeetingRoom;
    use InteractsWithRecord;

    protected static string $resource = MeetingResource::class;

    protected static bool $shouldRegisterNavigation = false;

    protected string $view = 'filament.resources.meetings.pages.go-meeting';

    public function mount(int|string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->initializeMeetingRoomFromRecord($this->record);
    }
}
