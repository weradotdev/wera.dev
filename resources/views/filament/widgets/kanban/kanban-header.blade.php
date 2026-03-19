@php
    $orderedBoardIds = $orderedBoardIds ?? [];
    $orderedBoardIdsJson = json_encode($orderedBoardIds);
    $statusIndex     = $status['index'] ?? 0;
    $statusId        = $status['id'];
    $canMoveLeft     = $statusIndex > 0;
    $canMoveRight    = $statusIndex < count($orderedBoardIds) - 1;
@endphp
<div class="flex items-center justify-between gap-2 mb-3 px-1">
    <div class="flex items-center gap-1.5 min-w-0 flex-1">
    <div class="flex items-center justify-between gap-2 w-full">
        <h3 class="font-semibold text-lg text-gray-700! dark:text-gray-200! truncate">
            {{ $status['title'] }}
        </h3>
        
        <x-filament::icon-button color="gray" outlined wire:click="startAddingCard({{ $status['id'] }})" icon="heroicon-o-plus" />
    </div>

        <div class="flex-1 flex items-center justify-between gap-0.5 shrink-0" wire:key="board-actions-{{ $statusId }}">
            <div class="items-center gap-0.5 hidden!">
                @if($canMoveLeft)
                    <x-filament::icon-button color="gray" icon="heroicon-o-arrow-left"
                        wire:click="moveBoardLeft({{ $statusId }}, {{ $orderedBoardIdsJson }})" label="Move list left" />
                @endif
                @if($canMoveRight)
                    <x-filament::icon-button color="gray" icon="heroicon-o-arrow-right"
                        wire:click="moveBoardRight({{ $statusId }}, {{ $orderedBoardIdsJson }})" label="Move list right" />
                @endif
            </div>
            @if(\Filament\Facades\Filament::getCurrentPanel()?->getId() === 'admin')
                <x-filament::dropdown>
                    <x-slot name="trigger">
                        <x-filament::icon-button color="gray" icon="heroicon-o-ellipsis-horizontal" label="More options" />
                    </x-slot>
                    <x-filament::dropdown.list>
                        <x-filament::dropdown.list.item icon="heroicon-o-trash" icon-color="danger"
                            wire:click="removeBoard({{ $statusId }})"
                            wire:confirm="Remove this list? Tasks in it will need to be moved to another list.">
                            Remove list
                        </x-filament::dropdown.list.item>
                    </x-filament::dropdown.list>
                </x-filament::dropdown>
            @endif
        </div>
    </div>
</div>
