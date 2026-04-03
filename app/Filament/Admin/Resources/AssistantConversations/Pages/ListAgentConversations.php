<?php

namespace App\Filament\Admin\Resources\AssistantConversations\Pages;

use App\Filament\Admin\Resources\AssistantConversations\AgentConversationResource;
use Filament\Resources\Pages\ListRecords;

class ListAgentConversations extends ListRecords
{
    protected static string $resource = AgentConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
