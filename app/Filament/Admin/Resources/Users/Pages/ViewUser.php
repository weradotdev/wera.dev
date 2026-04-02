<?php

namespace App\Filament\Admin\Resources\Users\Pages;

use App\Filament\Admin\Resources\Users\UserResource;
use App\Models\User;
use App\Notifications\GenericNotification;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('notify')
                ->schema([
                    Hidden::make('user_id')
                        ->default($this->record->id),
                    TextInput::make('subject'),
                    Textarea::make('message'),
                    ToggleButtons::make('channels')
                        ->options([
                            'database' => 'Database',
                            'fcm'      => 'Push Notifications',
                            'sms'      => 'SMS',
                            'mail'     => 'Email',
                        ])
                        ->multiple()
                        ->inline(),
                    KeyValue::make('links')
                        ->keyLabel('Link')
                        ->valueLabel('Label')
                        ->addActionLabel('Add link'),
                ])
                ->slideOver()
                ->action(fn (array $data) => User::find($data['user_id'])
                    ?->notify(new GenericNotification(
                        subject: $data['subject'],
                        message: $data['message'],
                        channels: $data['channels'],
                        links: $data['links']
                    )))
                ->color('info')
                ->modalWidth('lg'),
            EditAction::make(),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }
}
