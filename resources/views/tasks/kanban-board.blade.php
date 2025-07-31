
<x-filament-panels::page>

    <div class="flex justify-end items-center px-4 py-4 bg-gray-300 boder-gray-300 text-gray-700 dark:bg-gray-800 border dark:border-gray-800" style="border-radius: 30px">

        <div class="w-64">
            <input
                type="search"
                wire:model.live.bounce.500ms="search"
                placeholder="Search tasks..."
                class="border border-gray-300 rounded-lg px-3 py-2 w-full text-sm"
            />
        </div>
        
    </div>

    <div id="kanban-statuses" class="md:flex overflow-x-auto-hidden overflow-y-hidden gap-6 pb-4">
        @foreach($this->statuses as $status)
            @include(static::$statusView, ['status' => $status])
        @endforeach
    </div>

    <div wire:ignore>
        @include(static::$scriptsView)
    </div>

    @unless($disableEditModal)
        <x-filament-kanban::edit-record-modal/>
    @endunless

    <div 
        x-data="{ loading: false }"
        x-on:livewire:loading.window="loading = true"
        x-on:livewire:load.window="loading = false"
        x-on:livewire:message-processed.window="loading = false"
        class="relative"
    >

    <div
        x-show="loading"
        x-transition.opacity
        class="absolute inset-0 flex items-center justify-center bg-white/70 dark:bg-gray-800/70 z-50"
    >
        <svg class="animate-spin h-8 w-8 text-primary-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    </div>


</x-filament-panels::page>