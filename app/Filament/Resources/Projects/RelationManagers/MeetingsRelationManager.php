<?php

namespace App\Filament\Resources\Projects\RelationManagers;

use App\Filament\Resources\Projects\Resources\Meetings\MeetingResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MeetingsRelationManager extends RelationManager
{
    protected static string $relationship = 'meetings';

    protected static ?string $relatedResource = MeetingResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'ongoing'  => 'success',
                        'upcoming' => 'warning',
                        default    => 'gray',
                    }),
                TextColumn::make('host.name')
                    ->label('Host'),
                TextColumn::make('start_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                Action::make('go')
                    ->label('Go')
                    ->icon('heroicon-m-video-camera')
                    ->url(fn ($record): string => MeetingResource::getUrl('go', [
                        'parent' => $this->getOwnerRecord(),
                        'record' => $record,
                    ])),
            ]);
    }
}
