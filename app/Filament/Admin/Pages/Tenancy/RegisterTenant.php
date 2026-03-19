<?php

namespace App\Filament\Admin\Pages\Tenancy;

use App\Models\Workspace;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant as BaseRegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class RegisterTenant extends BaseRegisterTenant
{
    protected static ?int $navigationSort = 9;

    public static function getLabel(): string
    {
        return 'New workspace';
    }

    public static function canView(): bool
    {
        return auth()->check();
    }

    public function form(Schema $schema): Schema
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
                ColorPicker::make('color')
                ->required(),
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
            ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function handleRegistration(array $data): Model
    {
        $workspace = Workspace::create($data);

        $workspace->users()->syncWithoutDetaching([
            auth()->id() => ['role' => 'owner'],
        ]);

        return $workspace;
    }
}
