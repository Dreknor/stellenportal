@props([
    'type' => 'primary',
    'buttonType' => 'submit',
    'tag' => 'button',
])

@php
    $styleClasses = \Illuminate\Support\Arr::toCssClasses([
        'font-semibold py-2 px-4 rounded-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 transition-colors flex items-center justify-center gap-2 cursor-pointer',
        match ($type) {
            'danger' => 'text-white bg-red-700 hover:bg-red-800 focus-visible:ring-red-500',
            'secondary' => 'text-primary-800 bg-white border border-primary-200 hover:bg-primary-50 focus-visible:ring-primary-500 dark:bg-gray-800 dark:text-primary-200 dark:border-gray-600 dark:hover:bg-gray-700',
            'success' => 'text-white bg-green-700 hover:bg-green-800 focus-visible:ring-green-500',
            'warning' => 'text-white bg-amber-700 hover:bg-amber-800 focus-visible:ring-amber-500',
            'outline' => 'text-gray-700 bg-transparent border border-gray-300 hover:bg-gray-100 focus-visible:ring-gray-500 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-700',
            default => 'text-white bg-primary-600 hover:bg-primary-700 focus-visible:ring-primary-500',
        },
    ]);
@endphp

<{{ $tag }} {{ $attributes->merge(['class' => $styleClasses]) }}>
    {{ $slot }}
    </{{ $tag }}>
