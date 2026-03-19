<?php

namespace App\Filament\Admin\Pages\Tenancy;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\EditTenantProfile as BaseEditTenantProfile;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class EditTenantProfile extends BaseEditTenantProfile
{
    protected static ?int $navigationSort = 10;

    public static function getLabel(): string
    {
        return 'Edit Workspace';
    }

    public static function canView(Model $workspace): bool
    {
        return auth()->check();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->columnSpan(2),
                ColorPicker::make('color')
                    ->default('#0097b2')
                    ->columnSpan(1)
                    ->required(),
                Hidden::make('slug')
                    ->default(fn (): string => strtolower(uniqid())),
                Textarea::make('description')
                    ->default(null)
                    ->columnSpanFull(),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('avatars/workspaces')
                    ->imageEditor()
                    ->columnSpan(2),
                FileUpload::make('icon')
                    ->image()
                    ->disk('public')
                    ->directory('icons/workspaces')
                    ->imageEditor(),
            ])
            ->columns(3);
    }
}
