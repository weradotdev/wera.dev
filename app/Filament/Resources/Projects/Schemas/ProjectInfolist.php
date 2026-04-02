<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\User;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Project details')
                    ->schema([
                        ImageEntry::make('icon')
                            ->disk('public')
                            ->circular(),
                        ImageEntry::make('image')
                            ->disk('public')
                            ->circular(),
                        TextEntry::make('name'),
                        TextEntry::make('status'),
                        TextEntry::make('start_date')
                            ->date(),
                        TextEntry::make('end_date')
                            ->date(),
                        TextEntry::make('description')
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columns(6)
                    ->columnSpanFull(),
                Section::make(fn (Model $record) => 'Comments ('.$record->comments->count().')')
                    ->schema([
                        CommentsEntry::make('comments')
                            ->mentionables(fn (Model $record) => User::query()->orderBy('name')->get())
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }
}
