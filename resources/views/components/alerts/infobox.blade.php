@props([
    'type' => 'info'
])

<div x-data="{ showStatusMessage: true }" x-show="showStatusMessage"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform -translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform -translate-y-2"
     @switch($type)
        @case('info')
            class="mb-6 bg-blue-50 dark:bg-blue-900 border-l-4 border-blue-500 p-4 rounded-md"
            @break
        @case('warning')
            class="mb-6 bg-yellow-50 dark:bg-yellow-900 border-l-4 border-yellow-500 p-4 rounded-md"
            @break
        @case('error')
            class="mb-6 bg-red-50 dark:bg-red-900 border-l-4 border-red-500 p-4 rounded-md"
            @break
        @case('success')
            class="mb-6 bg-green-50 dark:bg-green-900 border-l-4 border-green-500 p-4 rounded-md"
            @break
        @default
            class="mb-6 bg-green-50 dark:bg-green-900 border-l-4 border-green-500 p-4 rounded-md"
     @endswitch
>

    <div class="flex items-center">
        {{ $slot }}
    </div>
</div>
