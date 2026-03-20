<?php

namespace App\Filament\Resources\Projects\Resources\Meetings;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Projects\Resources\Meetings\Pages\CreateMeeting;
use App\Filament\Resources\Projects\Resources\Meetings\Pages\EditMeeting;
use App\Filament\Resources\Projects\Resources\Meetings\Pages\GoMeeting;
use App\Filament\Resources\Projects\Resources\Meetings\Pages\ViewMeeting;
use App\Filament\Resources\Projects\Resources\Meetings\Schemas\MeetingForm;
use App\Filament\Resources\Projects\Resources\Meetings\Schemas\MeetingInfolist;
use App\Filament\Resources\Projects\Resources\Meetings\Tables\MeetingsTable;
use App\Models\Meeting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class MeetingResource extends Resource
{
    protected static ?string $model = Meeting::class;

    protected static bool $isScopedToTenant = false;

    protected static ?string $parentResource = ProjectResource::class;

    protected static ?string $slug = 'meet';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-video-camera';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return MeetingForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MeetingInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MeetingsTable::configure($table);
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
            'create' => CreateMeeting::route('/create'),
            'view'   => ViewMeeting::route('/{record}'),
            'go'     => GoMeeting::route('/{record}/go'),
            'edit'   => EditMeeting::route('/{record}/edit'),
        ];
    }
}
