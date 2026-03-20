<?php

namespace App\Filament\Admin\Resources\Projects\Schemas;

use App\Models\Project;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
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
                            ->default(fn(): ?int => auth()->id()),
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
                        ColorPicker::make('color')
                            ->default('#000000')
                            ->columnSpan(1),
                        Hidden::make('slug')
                            ->default(fn(): string => strtolower(uniqid())),
                        Textarea::make('description')
                            ->default(null)
                            ->columnSpanFull(),
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
                        Section::make('Integrations')
                            ->description('Connect GitHub, Slack, WhatsApp and configure notifications for tasks.')
                            ->schema(static::integrationFields())
                            ->columns(2)
                            ->collapsible()
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->columnSpan(2),
                Section::make('Media')
                    ->schema([
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
                        FileUpload::make('image')
                            ->label('Logo Image')
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
                            ->reorderable(),
                    ])
                    ->columnSpan(1),
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
                ->icon('hugeicons-github')
                ->schema([
                    Toggle::make('settings.github.connected')
                        ->label('Connect GitHub')
                        ->default($defaults['github']['connected'])
                        ->live(),
                    TextInput::make('settings.github.repo_url')
                        ->label('Repository URL')
                        ->url()
                        ->placeholder('https://github.com/org/repo')
                        ->visible(fn($get) => $get('settings.github.connected')),
                    Toggle::make('settings.github.create_issues_with_tasks')
                        ->label('Create GitHub issue for each task')
                        ->default($defaults['github']['create_issues_with_tasks'])
                        ->visible(fn($get) => $get('settings.github.connected')),
                ])
                ->columns(1)
                ->collapsible(),
            Section::make('WhatsApp')
                ->icon('hugeicons-whatsapp')
                ->schema([
                    Toggle::make('settings.whatsapp.connected')
                        ->label('Connect WhatsApp')
                        ->default($defaults['whatsapp']['connected'])
                        ->live(),
                    TextEntry::make('setup')
                        ->hiddenLabel()
                        ->state('Connect via QR. Optionally send task notifications to a group and mention assignees.')
                        ->visible(fn($get) => $get('settings.whatsapp.connected')),
                    TextInput::make('settings.whatsapp.session_id')
                        ->label('Session ID')
                        ->placeholder('project-1')
                        ->helperText('Set automatically after scanning QR in Connect WhatsApp, or use project-{id}.')
                        ->visible(fn($get) => $get('settings.whatsapp.connected')),
                    Toggle::make('settings.whatsapp.has_group')
                        ->label('Send notifications to a WhatsApp group')
                        ->default($defaults['whatsapp']['has_group'])
                        ->visible(fn($get) => $get('settings.whatsapp.connected')),
                    TextInput::make('settings.whatsapp.group_name')
                        ->label('Group name')
                        ->placeholder('Project Updates')
                        ->helperText('Label for your reference (e.g. project updates group).')
                        ->visible(fn($get) => $get('settings.whatsapp.connected') && $get('settings.whatsapp.has_group')),
                    TextInput::make('settings.whatsapp.group_jid')
                        ->label('Group JID')
                        ->placeholder('120363xxxxxxxxx@g.us')
                        ->helperText('Group identifier for sending messages (format: number@g.us). Get it from your WhatsApp/Baileys session.')
                        ->required(fn($get) => $get('settings.whatsapp.has_group'))
                        ->visible(fn($get) => $get('settings.whatsapp.connected') && $get('settings.whatsapp.has_group')),
                ])
                ->headerActions([
                    Action::make('connectWhatsApp')
                        ->label('Scan QR')
                        ->icon('heroicon-m-qr-code')
                        ->modalHeading('Scan WhatsApp QR code')
                        ->modalDescription('Open WhatsApp on your phone, tap Menu → Linked devices → Link a device, then scan the QR code below.')
                        ->modalContent(fn(?Model $record): View => view('filament.whatsapp-qr-modal', [
                            'sessionId' => "project-{$record?->id}",
                            'projectId' => $record?->id,
                        ]))
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close'),
                ])
                ->columns(1)
                ->collapsible(),
            Section::make('Slack')
                ->icon('hugeicons-slack')
                ->schema([
                    Toggle::make('settings.slack.connected')
                        ->label('Connect Slack')
                        ->default($defaults['slack']['connected'])
                        ->live(),
                    TextInput::make('settings.slack.webhook_url')
                        ->label('Webhook URL')
                        ->url()
                        ->placeholder('https://hooks.slack.com/services/...')
                        ->visible(fn($get) => $get('settings.slack.connected')),
                    TextInput::make('settings.slack.channel')
                        ->label('Channel')
                        ->placeholder('#general')
                        ->visible(fn($get) => $get('settings.slack.connected')),
                ])
                ->columns(1)
                ->collapsible(),
            Section::make('Telegram')
                ->icon('hugeicons-telegram')
                ->schema([
                    Toggle::make('settings.telegram.connected')
                        ->label('Connect Telegram')
                        ->default($defaults['telegram']['connected']),
                    TextInput::make('settings.telegram.bot_token')
                        ->label('Bot token')
                        ->password()
                        ->placeholder('123456:ABC-DEF...')
                        ->visible(fn($get) => $get('settings.telegram.connected')),
                ])
                ->columns(1)
                ->collapsible(),

            Section::make('Notifications')
                ->icon('hugeicons-notification-01')
                ->schema([
                    Toggle::make('settings.notifications.notify_developer_per_task')
                        ->label('Notify developer when assigned to a task')
                        ->default($defaults['notifications']['notify_developer_per_task'])
                        ->live(),
                    CheckboxList::make('settings.notifications.channels')
                        ->label('Notification channels')
                        ->columns(2)
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
        ];
    }
}
