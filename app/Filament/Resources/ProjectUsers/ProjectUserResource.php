<?php

namespace App\Filament\Resources\ProjectUsers;

use App\Filament\Resources\ProjectUsers\Pages\CreateProjectUser;
use App\Filament\Resources\ProjectUsers\Pages\EditProjectUser;
use App\Filament\Resources\ProjectUsers\Pages\ListProjectUsers;
use App\Filament\Resources\ProjectUsers\Pages\ViewProjectUser;
use App\Filament\Resources\ProjectUsers\RelationManagers\TasksRelationManager;
use App\Filament\Resources\ProjectUsers\Schemas\ProjectUserForm;
use App\Filament\Resources\ProjectUsers\Schemas\ProjectUserInfolist;
use App\Filament\Resources\ProjectUsers\Tables\ProjectUsersTable;
use App\Models\ProjectUser;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class ProjectUserResource extends Resource
{
    protected static ?string $model = ProjectUser::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static string|BackedEnum|null $navigationIcon = 'hugeicons-user-group';

    protected static ?string $navigationLabel = 'Team';

    protected static ?string $slug = 'team';

    protected static ?string $label = 'Team member';

    public static function form(Schema $schema): Schema
    {
        return ProjectUserForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectUsersTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProjectUserInfolist::configure($schema);
    }

    public static function getRelations(): array
    {
        return [
            TasksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListProjectUsers::route('/'),
            'create' => CreateProjectUser::route('/create'),
            'view'   => ViewProjectUser::route('/{record}'),
            'edit'   => EditProjectUser::route('/{record}/edit'),
        ];
    }
}
