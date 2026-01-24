@props(['model' => null, 'name' => 'logo'])

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ __('Logo') }}
    </label>

    @if($model && $model->getFirstMediaUrl('logo'))
        <div class="mb-4">
            <img src="{{ $model->getFirstMediaUrl('logo') }}" alt="Logo" class="max-w-xs rounded-lg shadow-md">
            <div class="mt-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remove_logo" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Logo entfernen') }}</span>
                </label>
            </div>
        </div>
    @endif

    <input
        type="file"
        name="{{ $name }}"
        accept="image/jpeg,image/png,image/jpg,image/webp"
        class="w-full px-4 py-2 rounded-lg text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
    >
    <div class="mt-2 space-y-1">
        <p class="text-sm text-gray-500 dark:text-gray-400">
            {{ __('Logo als Icon für die Anzeige. Erlaubte Formate: JPG, PNG, WEBP. Maximale Größe: 5 MB') }}
        </p>
        <p class="text-sm font-medium text-blue-600 dark:text-blue-400">
            <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ __('Empfohlene Bildgröße: 512×512 Pixel (Quadrat, transparent)') }}
        </p>
    </div>
    @error($name)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

