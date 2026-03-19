<?php

namespace App\Filament\Resources\Projects\Pages;

use App\Filament\Resources\Projects\ProjectResource;
use App\Filament\Widgets\BoardsKanbanWidget;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Kirschbaum\Commentions\Filament\Actions\CommentsAction;
use Kirschbaum\Commentions\Filament\Actions\SubscriptionAction;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CommentsAction::make()
                ->mentionables(User::query()->orderBy('name')->get()),
            SubscriptionAction::make()
                ->hiddenLabel(),
            EditAction::make()
                ->visible(fn () => auth()->id() === $this->record->user_id),
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

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return $this->record->name;
    }
}
