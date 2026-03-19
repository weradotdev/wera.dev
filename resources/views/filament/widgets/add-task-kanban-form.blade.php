<div>
    @if($boardId)
        <form wire:submit="submit">
            {{ $this->form }}

            <div class="mt-6 flex justify-end gap-2">
                <x-filament::button type="submit">
                    Add task
                </x-filament::button>
                <x-filament::button color="gray" type="button" wire:click="cancel">
                    Cancel
                </x-filament::button>
            </div>
        </form>
    @endif
</div>
