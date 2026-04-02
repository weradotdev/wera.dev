<?php

namespace App\Filament\Admin\Resources\Workspaces\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class WorkspaceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        $set('slug', Str::slug($state ?? ''));
                        $set('display_name', $state ?? '');
                    })
                    ->maxLength(255),
                TextInput::make('display_name')
                    ->maxLength(255),
                Hidden::make('slug')
                    ->default(fn (): string => strtolower(uniqid())),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->label('Logo image')
                    ->image()
                    ->disk('public')
                    ->directory('avatars/workspaces')
                    ->imageEditor(),
                FileUpload::make('image_dark')
                    ->label('Logo image (dark)')
                    ->image()
                    ->disk('public')
                    ->directory('avatars/workspaces/dark')
                    ->imageEditor(),
                FileUpload::make('icon')
                    ->label('Workspace icon')
                    ->image()
                    ->disk('public')
                    ->directory('icons/workspaces')
                    ->imageEditor(),
                FileUpload::make('icon_dark')
                    ->label('Workspace icon (dark)')
                    ->image()
                    ->disk('public')
                    ->directory('icons/workspaces/dark')
                    ->imageEditor(),
                Select::make('users')
                    ->multiple()
                    ->relationship('users', 'name')
                    ->columnSpanFull(),
            ]);
    }
}
