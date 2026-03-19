<?php

namespace App\Filament\Admin\Resources\Projects\Resources\Boards\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class BoardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                ColorPicker::make('color')
                    ->default(null),
                TextInput::make('position')
                    ->required()
                    ->numeric()
                    ->default(0),
            ])
            ->columns(3);
    }
}
