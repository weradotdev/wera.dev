<?php

namespace App\Filament\Admin\Resources\Projects\Resources\Meetings\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class MeetingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn (): ?int => filament()->auth()->id()),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                DateTimePicker::make('start_at'),
                DateTimePicker::make('end_at'),
            ])
            ->columns(2);
    }
}
