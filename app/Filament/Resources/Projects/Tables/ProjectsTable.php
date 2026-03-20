<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ColumnManagerLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;
use Kirschbaum\Commentions\Filament\Actions\SubscriptionAction;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('icon')
                    ->label('')
                    ->disk('public')
                    ->circular(),
                TextColumn::make('workspace.name')
                    ->label('Workspace')
                    ->searchable()
                    ->sortable()
                    ->placeholder('Personal'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'planning',
                        'success' => 'active',
                        'gray'    => 'on_hold',
                        'primary' => 'completed',
                    ])
                    ->sortable(),
                TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
            ])
            ->columnManagerLayout(ColumnManagerLayout::Modal)
            ->columnManagerTriggerAction(fn($action) => $action->slideOver())
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'planning'  => 'Planning',
                        'active'    => 'Active',
                        'on_hold'   => 'On Hold',
                        'completed' => 'Completed',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn(Project $record) => filament()->auth()->id() === $record->user_id),
                CommentsAction::make()
                    ->mentionables(User::query()->orderBy('name')->get()),
                SubscriptionAction::make()
                    ->hiddenLabel(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
