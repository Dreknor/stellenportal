@props(['section' => ''])

<div class="mb-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <x-icon name="fas-circle-info" class="h-5 w-5 text-blue-600 dark:text-blue-400" />
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm text-blue-700 dark:text-blue-300">
                {{ __('Benötigen Sie Hilfe?') }}
                <a href="{{ route('help') }}{{ $section ? '#' . $section : '' }}"
                   class="font-semibold underline hover:text-blue-800 dark:hover:text-blue-200 transition-colors">
                    {{ __('Zur Hilfe') }}
                </a>
            </p>
        </div>
    </div>
</div>

