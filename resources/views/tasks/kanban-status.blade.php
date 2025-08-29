@props(['status'])

<div 
    wire:key="kanban-status-{{ $status['id'] }}"
    class="status-column md:w-[18rem] flex-shrink-0 mb-5 md:min-h-full flex flex-col"
    data-id="{{$status['id']}}"
    >

    <div class="status-header px-4 pt-3 font-bold cursor-move">
        @include(static::$headerView)
    </div>

    <div
        wire:key="kanban-task-list-{{ $status['id'] }}"
        data-status-id="{{ $status['id'] }}"
        x-data
        class="flex flex-col flex-1 gap-2 p-3 bg-gray-200 dark:bg-gray-800 rounded-xl
        overflow-y-auto max-h-[70vh]"
        
    >
        @foreach($status['records'] as $record)
            @include(static::$recordView, ['record' => $record])
        @endforeach
    </div>
</div>
