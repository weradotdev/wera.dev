<?php

namespace App\Filament\Resources\ProjectUsers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectUserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User details')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Name'),
                        TextEntry::make('user.email')
                            ->label('Email'),
                        TextEntry::make('user.phone')
                            ->label('Phone')
                            ->placeholder('-'),
                        TextEntry::make('role')
                            ->badge(),
                        TextEntry::make('created_at')
                            ->label('Added on')
                            ->dateTime(),
                    ])
                    ->columns(6)
                    ->columnSpanFull()
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
