<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Project;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Tenancy\RegisterTenant;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class RegisterProjectTenant extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Create project';
    }

    public static function canView(): bool
    {
        return auth()->check();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('workspace_id')
                    ->label('Workspace')
                    ->options(fn (): array => auth()->user()?->workspaces()->pluck('name', 'workspaces.id')->all() ?? [])
                    ->required()
                    ->searchable(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Hidden::make('slug')
                    ->default(fn (): string => strtolower(uniqid())),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    protected function handleRegistration(array $data): Model
    {
        $project = Project::create([
            'workspace_id' => $data['workspace_id'],
            'name'         => $data['name'],
            'slug'         => $data['slug'] ?? strtolower(uniqid()),
            'description'  => $data['description'] ?? null,
            'status'       => 'active',
            'settings'     => Project::defaultSettings(),
        ]);

        $project->users()->syncWithoutDetaching([
            auth()->id() => ['role' => 'owner'],
        ]);

        return $project;
    }
}
