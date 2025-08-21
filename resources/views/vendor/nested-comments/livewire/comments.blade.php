<x-filament::section wire:poll.5s :compact="true" class="!ring-0 !shadow-none !p-0">
    <x-slot name="heading">
        <div class="flex items-center space-x-2">
            <div>{{ __('Comments') }}</div>
            <div>
                <x-filament::badge color="danger" :title="$this->comments->count()">
                    {{ \Illuminate\Support\Number::forHumans($this->comments->count(), maxPrecision: 3, abbreviate: true) }}
                </x-filament::badge>
            </div>
        </div>
    </x-slot>

    <div class="flex flex-col h-[450px]"> {{-- set a fixed height --}}
        {{-- Scrollable comments list --}}
        <x-slot name="headerEnd">
            <x-filament::button wire:click.prevent="refreshComments()">Refresh</x-filament::button>
        </x-slot>
        <div class="flex-1 overflow-y-auto pr-2 space-y-2">
            @foreach($this->comments as $comment)
                <livewire:nested-comments::comment-card
                    :key="$comment->getKey()"
                    :comment="$comment"
                />
            @endforeach
        </div>

        {{-- Always visible add-comment box --}}
        <div class="border-t pt-2 mt-2">
            <livewire:nested-comments::add-comment :commentable="$this->record" />
        </div>
    </div>
</x-filament::section>

