@props(['model'])

@if($model->getFirstMediaUrl('header_image') || $model->getFirstMediaUrl('logo'))
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">{{ __('Bilder') }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($model->getFirstMediaUrl('header_image'))
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Headerbild') }}</h3>
                    <img src="{{ $model->getFirstMediaUrl('header_image') }}" alt="Header Image" class="w-full rounded-lg shadow-md">
                </div>
            @endif

            @if($model->getFirstMediaUrl('logo'))
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Logo') }}</h3>
                    <img src="{{ $model->getFirstMediaUrl('logo') }}" alt="Logo" class="max-w-xs rounded-lg shadow-md">
                </div>
            @endif
        </div>
    </div>
@endif

