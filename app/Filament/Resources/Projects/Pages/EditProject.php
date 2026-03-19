<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Widgets\BoardsKanbanWidget;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
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
