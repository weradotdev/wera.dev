<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Models\Project;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Hidden::make('user_id')
                            ->default(fn (): ?int => filament()->auth()->id()),
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                $set('slug', Str::slug($state ?? ''));
                                $set('display_name', $state ?? '');
                            })
                            ->maxLength(255)
                            ->columnSpan(2),
                        TextInput::make('display_name')
                            ->maxLength(255)
                            ->columnSpan(2),
                        Hidden::make('slug')
                            ->default(fn (): string => strtolower(uniqid())),
                        Select::make('status')
                            ->required()
                            ->options([
                                'planning'  => 'Planning',
                                'active'    => 'Active',
                                'on_hold'   => 'On Hold',
                                'completed' => 'Completed',
                            ])
                            ->default('active'),
                        DatePicker::make('start_date'),
                        DatePicker::make('end_date'),
                        Textarea::make('description')
                            ->rows(4)
                            ->columnSpanFull(),
                        Select::make('users')
                            ->relationship('users', 'name', modifyQueryUsing: fn (Builder $query) => $query
                                ->whereHas('workspaces', fn (Builder $workspaceQuery) => $workspaceQuery->whereKey(Filament::getTenant())))
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpan(2),
                Section::make('Media')
                    ->schema([
                        FileUpload::make('image')
                            ->label('Logo image')
                            ->image()
                            ->disk('public')
                            ->directory('avatars/projects')
                            ->imageEditor(),
                        FileUpload::make('image_dark')
                            ->label('Logo image (dark)')
                            ->image()
                            ->disk('public')
                            ->directory('avatars/projects/dark')
                            ->imageEditor(),
                        FileUpload::make('banner')
                            ->label('Banner image')
                            ->image()
                            ->disk('public')
                            ->directory('banners/projects')
                            ->imageEditor(),
                        FileUpload::make('banner_dark')
                            ->label('Banner image (dark)')
                            ->image()
                            ->disk('public')
                            ->directory('banners/projects/dark')
                            ->imageEditor(),
                        SpatieMediaLibraryFileUpload::make('screenshots')
                            ->label('Screenshots / Attachments')
                            ->collection('screenshots')
                            ->multiple()
                            ->reorderable()
                            ->columnSpanFull(),
                        FileUpload::make('icon')
                            ->label('Project icon')
                            ->image()
                            ->disk('public')
                            ->directory('icons/projects')
                            ->imageEditor(),
                        FileUpload::make('icon_dark')
                            ->label('Project icon (dark)')
                            ->image()
                            ->disk('public')
                            ->directory('icons/projects/dark')
                            ->imageEditor(),
                    ])
                    ->columnSpan(1),
                Section::make('Integrations')
                    ->description('GitHub, Slack, WhatsApp and task notifications.')
                    ->schema(static::integrationFields())
                    ->columns(2)
                    ->collapsible()
                    ->columnSpanFull(),
            ])
            ->columns(3);
    }

    /**
     * @return array<int, Section>
     */
    protected static function integrationFields(): array
    {
        $defaults = Project::defaultSettings();

        return [
            Section::make('GitHub')
                ->schema([
                    Toggle::make('settings.github.connected')
                        ->label('Connected to GitHub')
                        ->default($defaults['github']['connected']),
                    TextInput::make('settings.github.repo_url')
                        ->label('Repository URL')
                        ->url()
                        ->placeholder('https://github.com/org/repo')
                        ->visible(fn ($get) => $get('settings.github.connected')),
                    Toggle::make('settings.github.create_issues_with_tasks')
                        ->label('Create GitHub issue for each task')
                        ->default($defaults['github']['create_issues_with_tasks'])
                        ->visible(fn ($get) => $get('settings.github.connected')),
                ])
                ->columns(1)
                ->collapsible(),
            Section::make('Notifications')
                ->schema([
                    Toggle::make('settings.notifications.notify_developer_per_task')
                        ->label('Notify when assigned to a task')
                        ->default($defaults['notifications']['notify_developer_per_task']),
                    Select::make('settings.notifications.channels')
                        ->label('Notification channels')
                        ->multiple()
                        ->options([
                            'email'    => 'Email',
                            'slack'    => 'Slack',
                            'telegram' => 'Telegram',
                            'whatsapp' => 'WhatsApp',
                        ])
                        ->default($defaults['notifications']['channels']),
                ])
                ->columns(1)
                ->collapsible(),
            Section::make('Slack')
                ->schema([
                    Toggle::make('settings.slack.connected')
                        ->label('Connected to Slack')
                        ->default($defaults['slack']['connected']),
                    TextInput::make('settings.slack.webhook_url')
                        ->label('Webhook URL')
                        ->url()
                        ->placeholder('https://hooks.slack.com/services/...')
                        ->visible(fn ($get) => $get('settings.slack.connected')),
                    TextInput::make('settings.slack.channel')
                        ->label('Channel')
                        ->placeholder('#general')
                        ->visible(fn ($get) => $get('settings.slack.connected')),
                ])
                ->columns(1)
                ->collapsible(),
            Section::make('Telegram')
                ->schema([
                    Toggle::make('settings.telegram.connected')
                        ->label('Connected to Telegram')
                        ->default($defaults['telegram']['connected']),
                    TextInput::make('settings.telegram.bot_token')
                        ->label('Bot token')
                        ->password()
                        ->placeholder('123456:ABC-DEF...')
                        ->visible(fn ($get) => $get('settings.telegram.connected')),
                ])
                ->columns(1)
                ->collapsible(),
            Section::make('WhatsApp')
                ->description('Connect via QR (use Connect WhatsApp when editing). Optionally send task notifications to a group and mention assignees.')
                ->schema([
                    Toggle::make('settings.whatsapp.connected')
                        ->label('Connected to WhatsApp')
                        ->default($defaults['whatsapp']['connected']),
                    TextInput::make('settings.whatsapp.session_id')
                        ->label('Session ID')
                        ->placeholder('project-1')
                        ->helperText('Set automatically after scanning QR in Connect WhatsApp, or use project-{id}.')
                        ->visible(fn ($get) => $get('settings.whatsapp.connected')),
                    Toggle::make('settings.whatsapp.has_group')
                        ->label('Send notifications to a WhatsApp group')
                        ->default($defaults['whatsapp']['has_group'])
                        ->visible(fn ($get) => $get('settings.whatsapp.connected')),
                    TextInput::make('settings.whatsapp.group_name')
                        ->label('Group name')
                        ->placeholder('Project Updates')
                        ->helperText('Label for your reference (e.g. project updates group).')
                        ->visible(fn ($get) => $get('settings.whatsapp.connected') && $get('settings.whatsapp.has_group')),
                    TextInput::make('settings.whatsapp.group_jid')
                        ->label('Group JID')
                        ->placeholder('120363xxxxxxxxx@g.us')
                        ->helperText('Group identifier for sending (format: number@g.us). Get it from your WhatsApp/Baileys session.')
                        ->required(fn ($get) => $get('settings.whatsapp.has_group'))
                        ->visible(fn ($get) => $get('settings.whatsapp.connected') && $get('settings.whatsapp.has_group')),
                ])
                ->columns(1)
                ->collapsible(),
        ];
    }
}
