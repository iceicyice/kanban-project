<div
    data-id="{{ $record->getKey() }}"
    id="{{ $record->getKey() }}"
    wire:key="record-{{ $record->getKey() }}"
    wire:click="recordClicked('{{ $record->getKey() }}', {{ @json_encode($record) }})"
    class="record bg-white dark:bg-gray-700 rounded-lg px-4 py-2 cursor-grab font-medium text-gray-600 dark:text-gray-200 border-l-8"
    style="border-color: {{ $record->color }}"
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
        <div class="flex flex-wrap justify-between">
            <div class="text-xs text-left text-gray-400"> {{ $record->user->name }} </div>
            <div class="text-xs float-right flex">
                <x-heroicon-s-clock class="w-4 h-4"/> {{$record['deadline']?->format('d M') ? : 'No Deadline'}}
            </div>
        </div>
        <div class="border-b" style="border-color: {{$record->color}}"></div>
        <div>
            {{ $record->{static::$recordTitleAttribute} }}
    
            @if ($record['urgent'])
                <x-heroicon-s-star class="inline-block text-pink-500 w-4 h-4"/>
            @endif
        </div>
    </div>

    <div class="text-xs text-gray-400 border-l-2 pl-1.5 mt-2 mb-2 break-words">
        {{ Str::limit($record['description'], 75) }}
    </div>

    <div class="flex hover:-space-x-1 -space-x-3">
        @foreach ($record->project->users->slice(0, 4) as $member)
            @php
                $avatarPath = $member->avatar_url;
                $hasAvatar = $avatarPath && Storage::disk('public')->exists($avatarPath);
                $initials = strtoupper(Str::limit($member->name, 2, ''));
            @endphp

            @if ($hasAvatar)
                <img 
                    src="{{ Storage::url($avatarPath) }}"
                    class="w-8 h-8 flex transition-all items-center justify-center text-xs font-semibold text-white rounded-full border-2"
                    {{-- style="background-color: {{ $record->color }}" --}}
                    title="{{ $member->name }}"
                >
            @else
                <div
                    class="w-8 h-8 flex transition-all items-center justify-center text-xs font-semibold text-white rounded-full border-2"
                    style="background-color: {{ $record->color }}"
                    title="{{ $member->name }}"
                >
                    {{ $initials }}
                </div>
            @endif
        @endforeach

        @if ($record->project->users->count() > 4)
            <div class="w-8 h-8 flex transition-all items-center justify-center text-xs font-semibold  text-gray-200 dark:text-gray-700 bg-gray-700 dark:bg-white rounded-full border-2">
                +{{ $record->project->users->count() - 4 }}
            </div>
        @endif
    </div>

    <div class="flex mt-1">
        <div class="border-2 rounded-md border-solid text-xs flex-none w-14 mt-1 mr-1 text-center border-indigo-200" style="border-left-color: {{$record->color}};border-bottom-color: {{$record->color}};"> {{$record->created_at->format('d M')}} </div>
        <div class="flex-auto">
            <div class="mt-2 relative">
                <div class="absolute h-2 rounded-full" style="width: {{ $record['progress'] }}%; background-color:{{$record->color}}"></div>
                <div class="h-2 bg-gray-200 rounded-full"></div>
            </div>
        </div>
        <div class="text-xs flex-none w-8 mt-1 ml-2"> {{$record['progress']}}%</div>
    </div>

    <div class="border-b mt-1 bg-gray-700 dark:border-gray-500"></div>

    <div class="flex flex-wrap gap-1.5 mt-1.5">
        <label class="text-xs" for="">Tags :</label>
        @foreach($record['tag'] as $tag)
            <span class="border rounded-md border-none text-xs bg-gray-300 dark:bg-gray-800 pl-1 pr-1 py-0.5 pb-0.5 ">
                {{ $tag }}
            </span>
        @endforeach
    </div>
</div>
