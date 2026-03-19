<?php

namespace App\Filament\Admin\Resources\Projects\Schemas;

use App\Models\Project;
use App\Models\User;
use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;
use Phiki\Grammar\Grammar;

class ProjectInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        TextEntry::make('workspace.name')
                            ->label('Workspace')
                            ->columnSpan(2),
                        TextEntry::make('status'),
                        TextEntry::make('start_date')
                            ->date()
                            ->placeholder('N/A'),
                        TextEntry::make('end_date')
                            ->date()
                            ->placeholder('N/A'),
                        Section::make()
                            ->description('To embed chat, Add this to your website’s body (preferably at the end)')
                            ->icon('hugeicons-source-code')
                            ->schema([
                                CodeEntry::make('embed_code')
                                ->hiddenLabel()
                                    ->default(fn(Project $record) => '<script> 
window.WERA_CHAT_EMBED_CONFIG = { 
    iframeUrl: "' . str_replace('ws.', '', asset(route('ticket', ['workspace' => $record->workspace->slug, 'project' => $record->slug], false))) . '", 
    buttonBackground: ' . json_encode($record->color ?: '#2d89ef') . ', 
    width: 420, 
    height: 640, 
    position: "bottom-left" 
};
</script>
<script src=' .str_replace('ws.', '', rtrim(asset('embed-chat.js'))) . '></script>')
                                    ->copyable()
                                    ->copyMessage('Iframe embed code copied')
                                    ->copyMessageDuration(1500)
                                    ->grammar(Grammar::Html)
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->collapsed()
                            ->columnSpanFull(),
                    ])
                    ->columns(5)
                    ->columnSpanFull(),
                CommentsEntry::make('comments')
                    ->mentionables(fn(Model $record) => User::query()->orderBy('name')->get())
                    ->columnSpanFull(),
            ]);
    }
}
