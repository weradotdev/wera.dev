<?php

namespace App\Filament\Admin\Resources\Projects\Resources\Boards;

use App\Filament\Admin\Resources\Projects\ProjectResource;
use App\Filament\Admin\Resources\Projects\Resources\Boards\Pages\CreateBoard;
use App\Filament\Admin\Resources\Projects\Resources\Boards\Pages\EditBoard;
use App\Filament\Admin\Resources\Projects\Resources\Boards\Pages\ViewBoard;
use App\Filament\Admin\Resources\Projects\Resources\Boards\Schemas\BoardForm;
use App\Filament\Admin\Resources\Projects\Resources\Boards\Schemas\BoardInfolist;
use App\Filament\Admin\Resources\Projects\Resources\Boards\Tables\BoardsTable;
use App\Models\Board;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BoardResource extends Resource
{
    protected static ?string $model = Board::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static bool $isScopedToTenant = false;

    protected static ?string $parentResource = ProjectResource::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return BoardForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return BoardInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BoardsTable::configure($table);
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
            'create' => CreateBoard::route('/create'),
            'view' => ViewBoard::route('/{record}'),
            'edit' => EditBoard::route('/{record}/edit'),
        ];
    }
}
