<div
    id="{{ $record->getKey() }}"
    wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})"
    class="record bg-white dark:bg-gray-700 rounded-lg px-4 py-2 cursor-grab font-medium text-gray-600 dark:text-gray-200"
    @if($record->timestamps && now()->diffInSeconds($record->{$record::UPDATED_AT}, true) < 3)
        x-data
        x-init="
            $el.classList.add('animate-pulse-twice', 'bg-primary-100', 'dark:bg-primary-800')
            $el.classList.remove('bg-white', 'dark:bg-gray-700')
            setTimeout(() => {
                $el.classList.remove('bg-primary-100', 'dark:bg-primary-800')
                $el.classList.add('bg-white', 'dark:bg-gray-700')
            }, 3000)
        "
    @endif
>

    <div class="">
        <div class="text-xs text-left text-gray-400"> {{ $record->user->name }} </div>
        <div>
            {{ $record->{static::$recordTitleAttribute} }}
    
            @if ($record['urgent'])
                <x-heroicon-s-star class="inline-block text-pink-500 w-4 h-4"/>
            @endif
        </div>

        
    </div>

    <div class="text-xs text-gray-400 border-l-4 pl-2 mt-2 mb-2">
        {{ $record->description }}
    </div>

    <div class="flex hover:-space-x-1 -space-x-3">
        @foreach ($record['team'] as $member)
            <div class="w-8 h-8 transition-all rounded-full bg-gray-200 border-2 border-white "></div>
        @endforeach
    </div>

    <div class="">
        <div class="mt-2 relative">
            <div class="absolute h-2 bg-sky-500 rounded-full" style="width: {{ $record['progress'] }}%"></div>
            <div class="h-2 bg-gray-200 rounded-full"></div>
        </div>
        <div class="text-xs"> {{$record['progress']}}%</div>
    </div>

</div>
