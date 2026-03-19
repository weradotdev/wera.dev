<?php

namespace App\Filament\Resources\ProjectUsers\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class ProjectUserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name', modifyQueryUsing: fn (Builder $query) => $query
                        ->whereHas('workspaces', fn (Builder $workspaceQuery) => $workspaceQuery->whereKey(Filament::getTenant())))
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('role')
                    ->required()
                    ->options([
                        'owner'   => 'Owner',
                        'manager' => 'Manager',
                        'member'  => 'Member',
                    ])
                    ->default('member'),
            ]);
    }
}
