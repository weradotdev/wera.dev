<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['workspace_id'] = null;

        return $data;
    }

    protected function afterCreate(): void
    {
        $user = auth()->user();
        $firstWorkspace = $user->workspaces()->first();

        if ($firstWorkspace) {
            $this->record->update(['workspace_id' => $firstWorkspace->getKey()]);
        }

        $this->record->users()->syncWithoutDetaching([
            $user->getKey() => ['role' => 'owner'],
        ]);
    }
}
