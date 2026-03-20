<?php

namespace App\Filament\Admin\Resources\Projects\Resources\Meetings\Pages;

use App\Filament\Admin\Resources\Projects\Resources\Meetings\MeetingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMeeting extends ViewRecord
{
    protected static string $resource = MeetingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
