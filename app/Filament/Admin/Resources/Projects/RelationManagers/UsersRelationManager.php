<?php

namespace App\Filament\Admin\Resources\Projects\RelationManagers;

use App\Filament\Admin\Resources\Users\UserResource;
use Filament\Actions\AttachAction;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $relatedResource = UserResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
                AttachAction::make()
                    ->multiple()
                    ->recordSelectSearchColumns(['name', 'email', 'phone'])
                    ->schema(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        Select::make('role')
                            ->options([
                                'admin'     => 'Admin',
                                'owner'     => 'Owner',
                                'developer' => 'Developer',
                            ])
                            ->required(),
                    ]),
            ])
            ->recordActions([
                AttachAction::make(),
            ]);

    }
}
