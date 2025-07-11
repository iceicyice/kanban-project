
<x-filament-panels::page>

    <div class="flex justify-end items-center px-4 py-4 text-gray-700">

        <div class="w-64">
            <input
                type="search"
                wire:model.live.bounce.500ms="search"
                placeholder="Search tasks..."
                class="border border-gray-300 rounded-lg px-3 py-2 w-full text-sm"
            />
        </div>
    </div>

    <div class="md:flex overflow-hidden overflow-y-hidden gap-4 pb-4">
        @foreach($this->statuses as $status)
            <div wire:key="kanban-status-{{ $status['id'] }}">
                @include(static::$statusView, ['status' => $status])
            </div>
        @endforeach

        <div wire:ignore>
            @include(static::$scriptsView)
        </div>
    </div>

    @unless($disableEditModal)
        <x-filament-kanban::edit-record-modal/>
    @endunless
</x-filament-panels::page>