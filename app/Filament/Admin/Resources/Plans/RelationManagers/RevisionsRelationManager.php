<?php

namespace App\Filament\Admin\Resources\Plans\RelationManagers;

use App\Models\PlanRevision;
use Filament\Actions\Action;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RevisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'revisions';

    protected static ?string $title = 'Revisions';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('description')
                    ->label('Description')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->slideOver()
                    ->modalIcon('heroicon-o-document-text')
                    ->modalWidth('lg')
                    ->modalSubheading('Description')
                    ->modalSubmitActionLabel('Close')
                    ->schema([
                        TextEntry::make('description')
                            ->label('Description')
                            ->state(fn (PlanRevision $record): string => new HtmlString(
                                Str::of($record->description)
                                    ->inlineMarkdown()
                            )),
                    ]),
            ]);
    }
}
