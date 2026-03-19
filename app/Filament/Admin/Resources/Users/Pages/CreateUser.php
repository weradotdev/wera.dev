<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use App\Notifications\UserInvitedWithWorkspaceNotification;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function afterCreate(): void
    {
        $workspace = $this->record->workspaces()->first();

        if ($workspace) {
            $this->record->notify(new UserInvitedWithWorkspaceNotification($workspace));
        }
    }
}
