<div x-data="{ open: @entangle('open') }" class="relative" wire:key="topbar-notifications">
    <!-- Notification Bell -->
    <button wire:click="toggle" class="relative p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 focus:outline-none">
        <svg class="w-6 h-6 text-gray-700 dark:text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>

        <!-- Unread Count -->
        @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full text-xs w-5 h-5 flex items-center justify-center">
                {{ $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Side Modal -->
    <div x-show="open"
         @click.away="open = false"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-x-full"
         x-transition:enter-end="opacity-100 transform translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform translate-x-0"
         x-transition:leave-end="opacity-0 transform translate-x-full"
         class="fixed top-0 right-0 z-50 h-full w-96 bg-white dark:bg-gray-800 shadow-lg overflow-y-auto border-l border-gray-200 dark:border-gray-700">

        <!-- Header -->
        <div class="flex justify-between items-center p-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-lg font-bold text-gray-900 dark:text-gray-100">Notifications</h2>
            <div class="flex space-x-2">
                <button wire:click="refreshNotifications" class="px-3 py-1 text-sm bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 rounded-md">
                    Refresh
                </button>
                <button wire:click="markAllAsRead" class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                    Mark All Read
                </button>
                <button @click="open = false" class="text-gray-500 hover:text-gray-800 dark:hover:text-gray-200 text-2xl">&times;</button>
            </div>
        </div>

        <!-- Notifications List -->
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($notifications as $notification)
                @php
                    $data = $notification['data'];
                @endphp

                <!-- Comment Notification -->
                @if(isset($data['comment_body']))
                    <div class="p-4 flex space-x-3 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <img src="{{ $data['commenter_avatar'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($data['commented_by'] ?? 'Guest') }}" alt="{{ $data['commented_by'] ?? 'Guest' }}" class="w-10 h-10 rounded-full object-cover flex-shrink-0">

                        <div class="flex-1 space-y-1">
                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                                {{ $data['commented_by'] ?? 'Guest' }} commented
                            </div>

                            <div class="text-sm text-gray-700 dark:text-gray-300 italic">
                                {!! $data['comment_body'] !!}
                            </div>

                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Task: <span class="font-medium">{{ $data['task_title'] ?? '' }}</span>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                Project: {{ $data['project_name'] ?? '' }}
                            </div>

                            <div class="mt-2 flex space-x-2">
                                <a href="{{ url('/tasks-kanban?project=' . ($data['project_id'] ?? '#')) }}"
                                   class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                                    View Project
                                </a>
                                <button wire:click="markAsRead('{{ $notification['id'] }}')"
                                        class="px-3 py-1 text-sm bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md">
                                    Mark Read
                                </button>
                            </div>
                        </div>
                    </div>

                <!-- Export Notification -->
                @elseif(isset($data['body']) && isset($data['actions'][0]['url']))
                    <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700">
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">
                            {{ $data['title'] ?? 'Export completed' }}
                        </div>
                        <div class="mt-1 text-gray-700 dark:text-gray-300">
                            {{ $data['body'] }}
                        </div>

                        <div class="mt-2 flex space-x-2">
                            <a href="{{ $data['actions'][0]['url'] }}" class="px-3 py-1 text-sm bg-blue-500 hover:bg-blue-600 text-white rounded-md">
                                Download .xlsx
                            </a>
                            <button wire:click="markAsRead('{{ $notification['id'] }}')" class="px-3 py-1 text-sm bg-gray-300 dark:bg-gray-700 hover:bg-gray-400 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-200 rounded-md">
                                Mark Read
                            </button>
                        </div>
                    </div>

                @else
                    <!-- Fallback for unknown notification types -->
                    <div class="p-4 text-gray-500 dark:text-gray-400">
                        Unknown notification type. Data: {{ json_encode($data) }}
                    </div>
                @endif
            @empty
                <div class="p-4 text-gray-500 dark:text-gray-400">No notifications</div>
            @endforelse
        </div>
    </div>
</div>
