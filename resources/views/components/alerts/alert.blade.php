@props(['message', 'status' => 'status'])

@php
    $colorClasses = [
        'status' => [
            'container' => 'bg-green-50 dark:bg-green-900 border-green-500',
            'text' => 'text-green-700 dark:text-green-200',
            'icon' => 'text-green-500 dark:text-green-400',
            'button' => 'text-green-500 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-800 focus:ring-green-500',
        ],
        'success' => [
            'container' => 'bg-green-50 dark:bg-green-900 border-green-500',
            'text' => 'text-green-700 dark:text-green-200',
            'icon' => 'text-green-500 dark:text-green-400',
            'button' => 'text-green-500 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-800 focus:ring-green-500',
        ],
        'primary' => [
            'container' => 'bg-blue-50 dark:bg-blue-900 border-blue-500',
            'text' => 'text-blue-700 dark:text-blue-200',
            'icon' => 'text-blue-500 dark:text-blue-400',
            'button' => 'text-blue-500 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-800 focus:ring-blue-500',
        ],
        'secondary' => [
            'container' => 'bg-gray-50 dark:bg-gray-900 border-gray-500',
            'text' => 'text-gray-700 dark:text-gray-200',
            'icon' => 'text-gray-500 dark:text-gray-400',
            'button' => 'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800 focus:ring-gray-500',
        ],
        'warning' => [
            'container' => 'bg-yellow-50 dark:bg-yellow-900 border-yellow-500',
            'text' => 'text-yellow-700 dark:text-yellow-200',
            'icon' => 'text-yellow-500 dark:text-yellow-400',
            'button' => 'text-yellow-500 dark:text-yellow-400 hover:bg-yellow-100 dark:hover:bg-yellow-800 focus:ring-yellow-500',
        ],
        'danger' => [
            'container' => 'bg-red-50 dark:bg-red-900 border-red-500',
            'text' => 'text-red-700 dark:text-red-200',
            'icon' => 'text-red-500 dark:text-red-400',
            'button' => 'text-red-500 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-800 focus:ring-red-500',
        ],
    ];

    $classes = $colorClasses[$status] ?? $colorClasses['success'];
@endphp

<div x-data="{ showStatusMessage: true }" x-show="showStatusMessage"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform -translate-y-2"
     x-transition:enter-end="opacity-100 transform translate-y-0"
     x-transition:leave="transition ease-in duration-300"
     x-transition:leave-start="opacity-100 transform translate-y-0"
     x-transition:leave-end="opacity-0 transform -translate-y-2"
     class="mb-6 border-l-4 p-4 rounded-md {{ $classes['container'] }}">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <svg class="h-5 w-5 {{ $classes['icon'] }}"
                 xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                      d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                      clip-rule="evenodd" />
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm {{ $classes['text'] }}">{{ session($status) }}</p>
        </div>
        <div class="ml-auto pl-3">
            <div class="-mx-1.5 -my-1.5">
                <button @click="showStatusMessage = false"
                        class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 {{ $classes['button'] }}">
                    <span class="sr-only">{{ __('Dismiss') }}</span>
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                         fill="currentColor">
                        <path fill-rule="evenodd"
                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                              clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
