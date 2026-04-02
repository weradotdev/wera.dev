<?php

namespace App\Filament\Admin\Resources\Tasks\Schemas;

use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class TaskForm
{
    public static function taskRoleOptions(): array
    {
        return [
            'developer' => 'Developer',
            'reviewer'  => 'Reviewer',
            'lead'      => 'Lead',
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Wizard::make()
                    ->steps([
                        Wizard\Step::make('Task details')
                            ->schema([
                                Hidden::make('user_id')
                                    ->default(fn (): ?int => filament()->auth()->id()),
                                Select::make('project_id')
                                    ->relationship('project', 'name', modifyQueryUsing: fn (Builder $query) => $query->whereBelongsTo(Filament::getTenant()))
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (Set $set): mixed => $set('board_id', null))
                                    ->required()
                                    ->searchable()
                                    ->preload(),
                                Select::make('board_id')
                                    ->label('Board')
                                    ->relationship(
                                        name: 'board',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where('project_id', $get('project_id'))
                                    )
                                    ->required()
                                    ->preload(),
                                Select::make('priority')
                                    ->required()
                                    ->options([
                                        'low'    => 'Low',
                                        'medium' => 'Medium',
                                        'high'   => 'High',
                                    ])
                                    ->default('medium'),
                                TextInput::make('title')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Textarea::make('description')
                                    ->default(null)
                                    ->columnSpanFull(),
                                Repeater::make('checklist')
                                    ->label('Checklist items')
                                    ->simple(
                                        TextInput::make('item')
                                            ->label('Item')
                                            ->required()
                                    )
                                    ->addActionLabel('Add item')
                                    ->columnSpanFull(),
                                SpatieMediaLibraryFileUpload::make('screenshots')
                                    ->label('Screenshots / Attachments')
                                    ->collection('screenshots')
                                    ->multiple()
                                    ->reorderable()
                                    ->columnSpanFull(),
                                DatePicker::make('due_at')
                                    ->label('Due Date'),
                                DateRangePicker::make('event_period')
                                    ->label('Schedule')
                                    ->withTime()
                                    ->singleField()
                                    ->format('Y-m-d H:i:s')
                                    ->displayFormat('M j, Y H:i')
                                    ->hidden(),
                                TextInput::make('position')
                                    ->required()
                                    ->numeric()
                                    ->default(0),
                            ])
                            ->columns(3),

                        Wizard\Step::make('Assigned Users')
                            ->schema([
                                Repeater::make('taskUsers')
                                    ->label('Assigned users')
                                    ->relationship()
                                    ->schema([
                                        Select::make('user_id')
                                            ->label('User')
                                            ->relationship(
                                                name: 'user',
                                                titleAttribute: 'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                                    ->whereHas('workspaces', fn (Builder $q) => $q->where('workspaces.id', Filament::getTenant()?->getKey()))
                                                    ->orderBy('name')
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload(),
                                        Select::make('role')
                                            ->options(static::taskRoleOptions())
                                            ->required()
                                            ->default('developer'),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->reorderable()
                                    ->addActionLabel('Add user'),

                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }
}
