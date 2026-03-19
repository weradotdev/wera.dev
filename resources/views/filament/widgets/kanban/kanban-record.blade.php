@php
    $record->loadMissing('assignedUsers');
    $assignedUsers = $record->assignedUsers ?? collect();
    $priority = $record->priority ?? 'medium';
    $progress = $record->progress ?? 0;
    $color = $record->board->color ?? '#eee';
@endphp
<div
    id="{{ $record->getKey() }}"
    wire:key="kanban-record-{{ $record->getKey() }}"
    wire:sort:item="{{ $record->getKey() }}"
    class="record kanban-task-card rounded-lg px-3 py-2.5 font-medium text-gray-900 dark:text-white shadow-sm border border-gray-200 dark:border-white/10 hover:shadow-md hover:border-gray-300 dark:hover:border-white/20 transition-shadow"
    @if($record->timestamps && now()->diffInSeconds($record->{$record::UPDATED_AT}, true) < 3)
        x-data
        x-init="
            $el.classList.add('animate-pulse-twice', 'bg-primary-50', 'dark:bg-primary-900/30', 'border-primary-200', 'dark:border-primary-800')
            $el.classList.remove('border-gray-200', 'dark:border-white/10')
            setTimeout(() => {
                $el.classList.remove('bg-primary-50', 'dark:bg-primary-900/30', 'border-primary-200', 'dark:border-primary-800')
                $el.classList.add('border-gray-200', 'dark:border-white/10')
            }, 3000)
        "
    @endif
>
    <div class="flex items-start gap-3">
        <div
            wire:sort:handle
            class="mt-0.5 flex h-6 w-6 shrink-0 cursor-grab items-center justify-center rounded-md text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:text-gray-500 dark:hover:bg-white/10 dark:hover:text-gray-300"
            title="Drag task"
            style="color: {{ $color }};"
        >
            <x-filament::icon icon="heroicon-m-bars-3" class="h-4 w-4" />
        </div>

        <div
            wire:sort:ignore
            wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})"
            class="min-w-0 flex-1 cursor-pointer"
        >
        <div class="truncate text-gray-900 dark:text-white">{{ $record->{static::$recordTitleAttribute} }}</div>
        <div class="mt-1.5 flex items-center justify-between gap-2">
            <span
                @class([
                    'inline-flex shrink-0 rounded px-1.5 py-0.5 text-xs font-medium capitalize',
                    'bg-gray-100 text-gray-700 dark:bg-gray-600/80 dark:text-white' => $priority === 'low',
                    'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200' => $priority === 'medium',
                    'bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-200' => $priority === 'high',
                ])
            >
                {{ $priority }}
            </span>
            @if($assignedUsers->isNotEmpty())
                <div class="flex -space-x-1.5 shrink-0" aria-hidden="true">
                    @foreach($assignedUsers as $user)
                        @php
                            $avatarUrl = method_exists($user, 'getFilamentAvatarUrl') ? $user->getFilamentAvatarUrl() : $user->avatar;
                        @endphp
                        <span
                            class="inline-flex ring-2 ring-white dark:ring-gray-800 rounded-full"
                            title="{{ $user->name }}"
                        >
                            <x-filament::avatar
                                :src="$avatarUrl"
                                :alt="$user->name"
                                size="sm"
                            />
                        </span>
                    @endforeach
                </div>
            @endif
        </div>
        <div
            class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
            role="progressbar"
            aria-valuenow="{{ $progress }}"
            aria-valuemin="0"
            aria-valuemax="100"
        >
            <div
                class="h-full rounded-full bg-primary-500 transition-[width] duration-300"
                style="width: {{ min(100, max(0, $progress)) }}%"
            ></div>
        </div>
        </div>
    </div>
</div>
