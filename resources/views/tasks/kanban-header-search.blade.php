<x-filament::header
    :heading="$this->getTitle()"
>
    <div class="flex items-center gap-4">
        <x-filament::input.wrapper>
            <x-filament::input type="search"
                name="search"
                wire:model.debounce.300ms="search"
                placeholder="Search tasks…"
                class="w-64"
/>
        </x-filament::input.wrapper>
    </div>
</x-filament::header>
