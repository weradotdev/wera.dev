<?php

namespace App\Concerns;

use App\Models\User;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Kirschbaum\Commentions\Filament\Infolists\Components\CommentsEntry;

trait HasEditRecordModal
{
    public bool $disableEditModal = false;

    public ?array $editModalFormState = [];

    public null|int|string $editModalRecordId = null;

    protected string $editModalTitle = 'Task Details';

    protected bool $editModalSlideOver = false;

    protected string $editModalWidth = '2xl';

    protected string $editModalSaveButtonLabel = 'Save';

    protected string $editModalCancelButtonLabel = 'Cancel';

    public function mount(): void
    {
        $this->form->fill();
    }

    public function recordClicked(int|string $recordId, array $data): void
    {
        $this->editModalRecordId = $recordId;

        /**
         * todo - the following line is a hacky fix
         * figure why sometimes form schema is created before this
         * method when a RichText is present in the form schema
         **/
        $this->form($this->form);
        $this->form->fill($this->getEditModalRecordData($recordId, $data));

        $this->dispatch('open-modal', id: 'kanban--edit-record-modal');
    }

    public function editModalFormSubmitted(): void
    {
        $this->editRecord($this->editModalRecordId, $this->form->getState(), $this->editModalFormState);

        $this->editModalRecordId = null;
        $this->form->fill();

        $this->dispatch('close-modal', id: 'kanban--edit-record-modal');
    }

    public function form(Schema $form): Schema
    {
        return $form
            ->schema($this->getEditModalFormSchema($this->editModalRecordId))
            ->statePath('editModalFormState')
            ->model($this->editModalRecordId ? static::$model::find($this->editModalRecordId) : static::$model);
    }

    protected function getEditModalRecordData(int|string $recordId, array $data): array
    {
        $record = $this->getEloquentQuery()->find($recordId);

        if (! $record instanceof Model) {
            return $data;
        }

        return $record->toArray();
    }

    protected function editRecord(int|string $recordId, array $data, array $state): void
    {
        $record = $this->getEloquentQuery()->find($recordId);

        if (! $record instanceof Model) {
            return;
        }

        $record->update($data);
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->record($this->editModalRecordId ? static::$model::find($this->editModalRecordId) : null)
            ->components([
                CommentsEntry::make('comments')
                    ->label('Comments')
                    ->columnSpanFull()
                    ->disableSidebar()
                    ->hideSubscribers()
                    ->mentionables(User::all())
                    ->extraAttributes([
                        'class' => 'flex-col',
                    ]),
            ]);
    }

    protected function getEditModalFormSchema(null|int|string $recordId): array
    {
        return [
            TextInput::make(static::$recordTitleAttribute),
            Textarea::make(static::$recordDescriptionAttribute),
        ];
    }

    protected function getEditModalTitle(): string
    {
        return $this->editModalTitle;
    }

    protected function getEditModalSlideOver(): bool
    {
        return $this->editModalSlideOver;
    }

    protected function getEditModalWidth(): string
    {
        return $this->editModalWidth;
    }

    protected function getEditModalSaveButtonLabel(): string
    {
        return $this->editModalSaveButtonLabel;
    }

    protected function getEditModalCancelButtonLabel(): string
    {
        return $this->editModalCancelButtonLabel;
    }
}
