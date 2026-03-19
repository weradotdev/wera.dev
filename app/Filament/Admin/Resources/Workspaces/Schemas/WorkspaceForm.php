<?php

namespace App\Filament\Admin\Resources\Workspaces\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class WorkspaceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Hidden::make('slug')
                    ->default(fn (): string => strtolower(uniqid())),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('avatars/workspaces')
                    ->imageEditor(),
                FileUpload::make('icon')
                    ->image()
                    ->disk('public')
                    ->directory('icons/workspaces')
                    ->imageEditor(),
                Select::make('users')
                    ->multiple()
                    ->relationship('users', 'name')
                    ->columnSpanFull(),
            ]);
    }
}
