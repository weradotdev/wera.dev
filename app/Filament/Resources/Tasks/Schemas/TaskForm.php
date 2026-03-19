<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\Board;
use CodeWithKyrian\FilamentDateRange\Forms\Components\DateRangePicker;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Hidden::make('user_id')
                    ->default(fn (): ?int => auth()->id()),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->rows(4)
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('screenshots')
                    ->label('Screenshots / Attachments')
                    ->collection('screenshots')
                    ->multiple()
                    ->reorderable()
                    ->columnSpanFull(),
                Select::make('board_id')
                    ->label('Board')
                    ->options(fn (): array => Board::query()
                        ->whereBelongsTo(Filament::getTenant())
                        ->orderBy('position')
                        ->get()
                        ->mapWithKeys(fn (Board $board): array => [$board->id => $board->name])
                        ->all())
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('priority')
                    ->required()
                    ->options([
                        'low'    => 'Low',
                        'medium' => 'Medium',
                        'high'   => 'High',
                    ])
                    ->default('medium'),
                DateRangePicker::make('event_period')
                    ->label('Schedule')
                    ->withTime()
                    ->singleField()
                    ->format('Y-m-d H:i:s')
                    ->displayFormat('M j, Y H:i'),
                TextInput::make('position')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->minValue(0),
            ])
            ->columns(5);
    }
}
