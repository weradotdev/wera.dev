<?php

namespace App\Filament\Admin\Resources\Projects\Resources\Meetings\Pages;

use App\Filament\Admin\Resources\Projects\Resources\Meetings\MeetingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMeeting extends CreateRecord
{
    protected static string $resource = MeetingResource::class;
}
