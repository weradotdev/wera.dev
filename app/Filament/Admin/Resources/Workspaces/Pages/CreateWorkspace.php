<?php

namespace App\Filament\Admin\Resources\Workspaces\Pages;

use App\Filament\Admin\Resources\Workspaces\WorkspaceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkspace extends CreateRecord
{
    protected static string $resource = WorkspaceResource::class;

    protected function afterCreate(): void
    {
        $userId = auth()->id();

        if (blank($userId)) {
            return;
        }

        $this->getRecord()->users()->syncWithoutDetaching([
            $userId => ['role' => 'owner'],
        ]);
    }
}
