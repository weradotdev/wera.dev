<?php

namespace App\Filament\Resources\Tasks\Pages;

use App\Filament\Resources\Tasks\TaskResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTasks extends ListRecords
{
    protected static string $resource = TaskResource::class;

    protected ?string $heading = 'Tasks assigned to me';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
