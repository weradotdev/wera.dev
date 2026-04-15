<?php

namespace App\Filament\Admin\Resources\AssistantConversations;

use App\Filament\Admin\Resources\AssistantConversations\Pages\ListAgentConversations;
use App\Filament\Admin\Resources\AssistantConversations\Pages\ViewAgentConversation;
use App\Models\ProjectConversation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AgentConversationResource extends Resource
{
    protected static ?string $model = ProjectConversation::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static \UnitEnum|string|null $navigationGroup = 'Assistant';

    protected static ?string $navigationLabel = 'Conversations';

    protected static ?int $navigationSort = 20;

    protected static bool $isScopedToTenant = false;

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->limit(8)
                    ->tooltip(fn ($record) => $record->id)
                    ->copyable(),
                TextColumn::make('project.name')
                    ->label('Project')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('channel')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'whatsapp' => 'success',
                        'telegram' => 'info',
                        'mobile'   => 'primary',
                        default    => 'gray',
                    }),
                TextColumn::make('messages_count')
                    ->label('Messages')
                    ->counts('messages')
                    ->sortable(),
                TextColumn::make('conversation.title')
                    ->label('Title')
                    ->limit(40)
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('updated_at')
                    ->label('Last activity')
                    ->since()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('channel')
                    ->options([
                        'mobile'   => 'Mobile',
                        'whatsapp' => 'WhatsApp',
                        'telegram' => 'Telegram',
                    ]),
            ])
            ->recordUrl(fn ($record) => ViewAgentConversation::getUrl(['record' => $record]));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAgentConversations::route('/'),
            'view'  => ViewAgentConversation::route('/{record}'),
        ];
    }
}
