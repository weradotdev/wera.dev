<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Widgets\BoardsKanbanWidget;
use App\Models\Meeting;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Contracts\View\View;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;
use Kirschbaum\Commentions\Filament\Actions\SubscriptionAction;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('startMeeting')
                ->label('Start Meeting')
                ->icon('heroicon-m-video-camera')
                ->visible(fn () => filament()->auth()->id() === $this->record->user_id)
                ->form([
                    Select::make('attendees')
                        ->label('Invite attendees')
                        ->multiple()
                        ->searchable()
                        ->options(
                            $this->record->users()
                                ->where('users.id', '!=', filament()->auth()->id())
                                ->orderBy('first_name')
                                ->get(['users.id', 'first_name', 'last_name', 'email'])
                                ->mapWithKeys(fn (User $user): array => [
                                    $user->id => trim($user->first_name.' '.$user->last_name).' ('.$user->email.')',
                                ])
                                ->all()
                        ),
                ])
                ->action(function (array $data): void {
                    $meeting = Meeting::query()->create([
                        'project_id'   => $this->record->id,
                        'host_user_id' => filament()->auth()->id(),
                        'title'        => $this->record->name.' meeting',
                        'status'       => 'live',
                        'started_at'   => now(),
                    ]);

                    $meeting->meetingUsers()->create([
                        'user_id'            => filament()->auth()->id(),
                        'invited_by_user_id' => filament()->auth()->id(),
                        'is_host'            => true,
                        'joined_at'          => now(),
                    ]);

                    foreach (($data['attendees'] ?? []) as $attendeeId) {
                        $meeting->meetingUsers()->firstOrCreate(
                            ['user_id' => (int) $attendeeId],
                            [
                                'invited_by_user_id' => filament()->auth()->id(),
                                'is_host'            => false,
                            ]
                        );
                    }

                    $this->redirect(ProjectResource::getUrl('do', [
                        'record'  => $this->record,
                        'meeting' => $meeting->getKey(),
                    ]), navigate: true);
                }),
            Action::make('connectWhatsApp')
                ->label('Connect WhatsApp')
                ->icon('heroicon-m-qr-code')
                ->modalHeading('Scan WhatsApp QR code')
                ->modalDescription('Open WhatsApp on your phone, tap Menu → Linked devices → Link a device, then scan the QR code below.')
                ->modalContent(fn (): View => view('filament.whatsapp-qr-modal', [
                    'sessionId' => 'project-'.$this->record->id,
                    'projectId' => $this->record->id,
                ]))
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close'),
            CommentsAction::make()
                ->mentionables(User::query()->orderBy('name')->get()),
            SubscriptionAction::make()
                ->hiddenLabel(),
            DeleteAction::make(),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            BoardsKanbanWidget::make([
                'record' => $this->record,
            ]),
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }
}
