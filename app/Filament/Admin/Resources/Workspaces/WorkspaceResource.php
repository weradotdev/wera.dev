<?php

namespace App\Filament\Admin\Resources\Workspaces;

use App\Filament\Admin\Resources\Workspaces\Pages\CreateWorkspace;
use App\Filament\Admin\Resources\Workspaces\Pages\EditWorkspace;
use App\Filament\Admin\Resources\Workspaces\Pages\ListWorkspaces;
use App\Filament\Admin\Resources\Workspaces\Schemas\WorkspaceForm;
use App\Filament\Admin\Resources\Workspaces\Tables\WorkspacesTable;
use App\Models\Workspace;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class WorkspaceResource extends Resource
{
    protected static ?string $model = Workspace::class;

    protected static bool $isScopedToTenant = false;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = 'hugeicons-building-02';

    protected static ?int $navigationSort = 8;

    public static function form(Schema $schema): Schema
    {
        return WorkspaceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return WorkspacesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListWorkspaces::route('/'),
            'create' => CreateWorkspace::route('/create'),
            'edit'   => EditWorkspace::route('/{record}/edit'),
        ];
    }
}
