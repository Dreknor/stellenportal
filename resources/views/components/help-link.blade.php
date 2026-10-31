@props(['section' => ''])

<div class="mb-6 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-lg p-4">
    <div class="flex items-start">
        <div class="flex-shrink-0">
            <x-icon name="fas-circle-info" class="h-5 w-5 text-primary-600 dark:text-primary-400" />
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm text-primary-700 dark:text-primary-300">
                {{ __('Benötigen Sie Hilfe?') }}
                <a href="{{ route('help') }}{{ $section ? '#' . $section : '' }}"
                   class="font-semibold underline hover:text-primary-800 dark:hover:text-primary-200 transition-colors">
                    {{ __('Zur Hilfe') }}
                </a>
            </p>
        </div>
    </div>
</div>

