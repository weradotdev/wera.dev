<?php

namespace App\Filament\Admin\Resources\Projects\Resources\Meetings\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class MeetingInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title')
                    ->placeholder('-'),
                TextEntry::make('status')
                    ->badge(),
                TextEntry::make('host.name')
                    ->label('Host'),
                TextEntry::make('start_at')
                    ->label('Scheduled start')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('end_at')
                    ->label('Scheduled end')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('started_at')
                    ->label('Actual start')
                    ->dateTime()
                    ->placeholder('Not yet started'),
                TextEntry::make('ended_at')
                    ->label('Actual end')
                    ->dateTime()
                    ->placeholder('Not yet ended'),
            ]);
    }
}
