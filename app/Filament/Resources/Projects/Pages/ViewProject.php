<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Resources\Projects\Resources\Meetings\MeetingResource;
use App\Filament\Widgets\BoardsKanbanWidget;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;
use Kirschbaum\Commentions\Filament\Actions\SubscriptionAction;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('meetings')
                ->label('Meetings')
                ->icon('heroicon-m-video-camera')
                ->url(fn (): string => MeetingResource::getUrl('create', ['parent' => $this->record])),
            CommentsAction::make()
                ->mentionables(User::query()->orderBy('name')->get()),
            SubscriptionAction::make()
                ->hiddenLabel(),
            EditAction::make()
                ->visible(fn () => filament()->auth()->id() === $this->record->user_id),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            BoardsKanbanWidget::make([
                'record' => $this->record,
            ]),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return $this->record->name;
    }

    public function getHeading(): string|Htmlable|null
    {
        return "{$this->record->name} ({$this->record->workspace->name})";
    }
}
