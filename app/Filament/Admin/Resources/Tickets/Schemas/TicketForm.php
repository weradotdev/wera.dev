<?php

namespace App\Filament\Admin\Resources\Tickets\Schemas;

use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('workspace_id')
                    ->default(fn (): ?int => Filament::getTenant()?->getKey()),
                Select::make('project_id')
                    ->relationship(
                        'project',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant())
                    )
                    ->required()
                    ->searchable()
                    ->preload()
                    ->columnSpan(2),
                Select::make('status')
                    ->required()
                    ->options([
                        'open'     => 'Open',
                        'assigned' => 'Assigned',
                        'closed'   => 'Closed',
                    ])
                    ->default('open')
                    ->columnSpan(1),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
