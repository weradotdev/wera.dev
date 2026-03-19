<x-filament-widgets::widget>
    <div
        x-data
        wire:key="kanban-board-{{ $kanbanRefreshKey }}"
        class="flex overflow-x-auto overflow-y-hidden gap-4 pb-4 w-full min-w-0"
    >
        @foreach ($statuses as $status)
            @include(static::$statusView, array_merge(compact('status'), ['orderedBoardIds' => $orderedBoardIds ?? []]))
        @endforeach

        @if(\Filament\Facades\Filament::getCurrentPanel()?->getId() === 'admin')
            <div class="min-w-80 shrink-0">
                @if($showAddListForm ?? false)
                    <div class="rounded-lg bg-gray-100/90 dark:bg-white/5 dark:border dark:border-white/10 p-3 shadow-sm w-72">
                        <x-filament::input.wrapper class="mb-2">
                            <x-filament::input
                                type="text"
                                wire:model="newBoardName"
                                wire:keydown.enter="createBoard"
                                placeholder="Enter board title..."
                            />
                        </x-filament::input.wrapper>
                        <div class="flex items-center gap-2">
                            <x-filament::button
                                size="sm"
                                wire:click="createBoard"
                            >
                                Add board
                            </x-filament::button>
                            <x-filament::icon-button
                                color="gray"
                                icon="heroicon-o-x-mark"
                                wire:click="toggleAddListForm"
                                label="Cancel"
                            />
                        </div>
                    </div>
                @else
                    <x-filament::button
                        color="gray"
                        outlined
                        wire:click="toggleAddListForm"
                        class="w-full justify-start min-h-16 border-2 border-dashed border-gray-300 dark:border-white/20"
                        icon="heroicon-o-plus"
                    >
                        Add board
                    </x-filament::button>
                @endif
            </div>
        @endif
    </div>

    @unless ($disableEditModal)
        <x-filament-kanban::edit-record-modal />
    @endunless

    <x-filament::modal id="kanban--add-task-modal" slideOver width="lg">
        <x-slot name="header">
            <x-filament::modal.heading>
                Add task
            </x-filament::modal.heading>
        </x-slot>

        @if($addingCardToBoardId ?? null)
            <livewire:filament.widgets.add-task-kanban-form :board-id="$addingCardToBoardId" :key="'add-task-'.$addingCardToBoardId" />
        @endif
    </x-filament::modal>
</x-filament-widgets::widget>
