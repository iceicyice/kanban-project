<div>
    @if($this->addingComment)
        <form wire:submit.prevent="create" wire:loading.attr="disabled" class="space-y-4">
            {{ $this->form }}
            <x-filament::button type="submit">
                Submit
            </x-filament::button>
            <x-filament::button type="button" color="gray" wire:click="showForm(false)">
                Cancel
            </x-filament::button>
        </form>
    @else
        <x-filament::input.wrapper
                :inline-prefix="true"
                prefix-icon="heroicon-o-chat-bubble-bottom-center-text">
            <x-filament::input
                    :placeholder="$this->replyTo?->getKey() ? 'Add a reply' : 'Add a new comment'"
                    type="text"
                    wire:click.prevent.stop="showForm(true)"
                    :readonly="true"
            />
        </x-filament::input.wrapper>
    @endif
    <x-filament-actions::modals />
    <style>
        /* Target images inside the mention suggestion buttons */
        .tippy-content img.object-cover {
            width: 40px !important;   
            height: 40px !important;  
            border-radius: 9999px;
            object-fit: cover;
            margin-right: 7px;
        }
        
        /* Kill the default orange selected */
        .tippy-content .mention-dropdown [aria-selected="true"],
        .tippy-content .mention-dropdown .bg-primary-500 {
            background-color: transparent !important;
            color: inherit !important;
        }

        /* Light mode → dark hover */
        .tippy-content .mention-dropdown button:hover {
            background-color: #374151 !important; /* gray-700 */
            color: white !important;
        }

        /* Dark mode → light hover */
        .dark .tippy-content .mention-dropdown button:hover {
            background-color: #f3f4f6 !important; /* gray-100 */
            color: black !important;
        }

    </style>
</div>
