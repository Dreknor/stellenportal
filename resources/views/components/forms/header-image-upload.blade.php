@props(['model' => null, 'name' => 'header_image'])

<div class="mb-4">
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ __('Headerbild') }}
    </label>

    @if($model && $model->getFirstMediaUrl('header_image'))
        <div class="mb-4">
            <img src="{{ $model->getFirstMediaUrl('header_image') }}" alt="Header Image" class="max-w-md rounded-lg shadow-md">
            <div class="mt-2">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="remove_header_image" value="1" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Headerbild entfernen') }}</span>
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
    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
        {{ __('Erlaubte Formate: JPG, PNG, WEBP. Maximale Größe: 5 MB') }}
    </p>
    @error($name)
        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
</div>

