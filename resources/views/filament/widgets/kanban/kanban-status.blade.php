@props(['status', 'orderedBoardIds' => []])

<div wire:key="status-{{ $status['id'] }}"
    class="min-w-80 w-80 shrink-0 flex flex-col min-h-0 pr-4 border-r border-gray-200 dark:border-white/10">
    @include(static::$headerView, ['status' => $status, 'orderedBoardIds' => $orderedBoardIds])

    <div
        wire:sort="sortTask"
        wire:sort:group="kanban-tasks"
        wire:sort:group-id="{{ $status['id'] }}"
        data-status-id="{{ $status['id'] }}"
        class="flex flex-col flex-1 gap-2 min-h-8"
    >
        @foreach($status['records'] as $record)
            @include(static::$recordView)
        @endforeach
    </div>
</div>
