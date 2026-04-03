<?php

namespace App\Filament\Admin\Resources\AssistantConversations\Pages;

use App\Filament\Admin\Resources\AssistantConversations\AgentConversationResource;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewAgentConversation extends ViewRecord
{
    protected static string $resource = AgentConversationResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Conversation')
                ->schema([
                    TextEntry::make('id')->label('ID'),
                    TextEntry::make('project.name')->label('Project'),
                    TextEntry::make('user.name')->label('User'),
                    TextEntry::make('channel')->badge(),
                    TextEntry::make('conversation.title')->label('Title'),
                    TextEntry::make('created_at')->dateTime(),
                    TextEntry::make('updated_at')->label('Last activity')->dateTime(),
                ])
                ->columns(2),

            Section::make('Messages')
                ->schema([
                    RepeatableEntry::make('conversation.messages')
                        ->schema([
                            TextEntry::make('role')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'user'      => 'primary',
                                    'assistant' => 'success',
                                    default     => 'gray',
                                }),
                            TextEntry::make('content')
                                ->columnSpan(3),
                            TextEntry::make('meta.mode')
                                ->label('Mode')
                                ->placeholder('—'),
                            TextEntry::make('created_at')
                                ->since(),
                        ])
                        ->columns(5),
                ]),
        ]);
    }
}
